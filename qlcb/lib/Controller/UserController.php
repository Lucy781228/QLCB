<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Lucy <ct040407@actv.edu.vn>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\QLCB\Controller;

use OCP\IRequest;
use OCP\IDBConnection;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\OCS\OCSNotFoundException;

use OCP\IUserSession;
use OCP\IGroupManager;

class UserController extends Controller
{
    private $db;

    protected $userSession;

    protected $groupManager;

    public function __construct(
        $AppName,
        IRequest $request,
        IDBConnection $db,
        IUserSession $userSession,
        IGroupManager $groupManager
    ) {
        parent::__construct($AppName, $request, $userSession, $groupManager);
        $this->db = $db;
        $this->userSession = $userSession;
        $this->groupManager = $groupManager;
    }

    /**
     * @NoCSRFRequired
     */
    public function createUser(
        $qlcb_uid,
        $full_name,
        $date_of_birth,
        $gender,
        $phone,
        $address,
        $id_number,
        $email,
        $position_id,
        $coefficients_salary,
        $tax,
        $communist_party_joined,
        $unit_id,
        $day_joined
    ) {
        $query = $this->db->getQueryBuilder();
        $query
            ->insert("qlcb_user")
            ->values([
                "qlcb_uid" => $query->createNamedParameter($qlcb_uid),
                "full_name" => $query->createNamedParameter($full_name),
                "date_of_birth" => $query->createNamedParameter($date_of_birth),
                "gender" => $query->createNamedParameter($gender),
                "phone" => $query->createNamedParameter($phone),
                "address" => $query->createNamedParameter($address),
                "id_number" => $query->createNamedParameter($id_number),
                "email" => $query->createNamedParameter($email),
                "position_id" => $query->createNamedParameter($position_id),
                "coefficients_salary" => $query->createNamedParameter(
                    $coefficients_salary
                ),
                "tax" => $query->createNamedParameter($tax),
                "communist_party_joined" => $query->createNamedParameter(
                    $communist_party_joined
                ),
                "unit_id" => $query->createNamedParameter($unit_id),
                "day_joined" => $query->createNamedParameter($day_joined),
            ])
            ->execute();

        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function getUser($qlcb_uid)
    {
        $query = $this->db->getQueryBuilder();

        $query
            ->select("*")
            ->from("qlcb_user")
            ->where(
                $query
                    ->expr()
                    ->eq("qlcb_uid", $query->createNamedParameter($qlcb_uid))
            );

        $result = $query->execute();
        $data = $result->fetch();

        return ["user" => $data];
    }

    /**
     * @NoCSRFRequired
     */
    public function getNCUsers()
    {
        $query = $this->db->getQueryBuilder();
        $query->select("qlcb_uid")->from("qlcb_user");
        $result = $query->execute();
        $rows = $result->fetchAll();
        $qlcbUids = array_column($rows, "qlcb_uid");
        // $result->closeCursor();

        $userManager = \OC::$server->getUserManager();
        $allUsers = $userManager->search("");

        $filteredUsers = array_filter($allUsers, function ($user) use (
            $qlcbUids
        ) {
            return !in_array($user->getUID(), $qlcbUids);
        });

        $userList = array_map(function ($user) {
            return [
                "user_id" => $user->getUID(),
                "display_name" => $user->getDisplayName(),
            ];
        }, $filteredUsers);

        return ["nc_users" => $userList];
    }

    /**
     * @NoCSRFRequired
     */
    public function getAllUsers()
    {
        $query = $this->db->getQueryBuilder();

        $query
            ->select("u.*", "unit.unit_name", "position.position_name")
            ->from("qlcb_user", "u")
            ->leftJoin(
                "u",
                "qlcb_unit",
                "unit",
                $query->expr()->eq("u.unit_id", "unit.unit_id")
            )
            ->leftJoin(
                "u",
                "qlcb_position",
                "position",
                $query->expr()->eq("u.position_id", "position.position_id")
            );

        $result = $query->execute();
        $data = $result->fetchAll();

        return ["users" => $data];
    }

    protected function filterBusiness($query)
    {
        $query->innerJoin(
            "u",
            "qlcb_business",
            "business",
            "u.qlcb_uid = business.qlcb_uid"
        );
    }

    protected function filterEducation($query)
    {
        $query->innerJoin(
            "u",
            "qlcb_education",
            "education",
            "u.qlcb_uid = education.qlcb_uid"
        );
    }

    protected function filterBonusTrue($query)
    {
        $query->innerJoin(
            "u",
            "qlcb_bonus",
            "bonusTrue",
            "u.qlcb_uid = bonusTrue.qlcb_uid AND bonusTrue.type = 1"
        );
    }

    protected function filterBonusFalse($query)
    {
        $query->innerJoin(
            "u",
            "qlcb_bonus",
            "bonusFalse",
            "u.qlcb_uid = bonusFalse.qlcb_uid AND bonusFalse.type = 0"
        );
    }

    protected function applyDynamicFilters($query, array $types)
    {
        $filterMap = [
            "1" => "filterBusiness",
            "2" => "filterEducation",
            "3" => "filterBonusTrue",
            "4" => "filterBonusFalse",
        ];

        foreach ($types as $type) {
            if (isset($filterMap[$type])) {
                $this->{$filterMap[$type]}($query);
            }
        }
    }

    /**
     * @NoCSRFRequired
     */
    public function getUsersByType(array $types)
    {
        $query = $this->db->getQueryBuilder();

        $query
            ->select("u.*", "unit.unit_name", "position.position_name")
            ->from("qlcb_user", "u")
            ->leftJoin(
                "u",
                "qlcb_unit",
                "unit",
                $query->expr()->eq("u.unit_id", "unit.unit_id")
            )
            ->leftJoin(
                "u",
                "qlcb_position",
                "position",
                $query->expr()->eq("u.position_id", "position.position_id")
            )
            ->groupBy("u.qlcb_uid");

        $this->applyDynamicFilters($query, $types);

        $result = $query->execute();
        return ["users" => $result->fetchAll()];
    }

    /**
     * @NoCSRFRequired
     */
    public function updateUser(
        $qlcb_uid,
        $full_name,
        $date_of_birth,
        $gender,
        $phone,
        $address,
        $id_number,
        $email,
        $position_id,
        $coefficients_salary,
        $tax,
        $communist_party_joined,
        $unit_id,
        $day_joined
    ) {
        $query = $this->db->prepare('UPDATE `oc_qlcb_user` SET 
        `full_name` = COALESCE(?, `full_name`), 
        `date_of_birth` = COALESCE(?, `date_of_birth`),
        `gender` = COALESCE(?, `gender`),
        `phone` = COALESCE(?, `phone`),
        `address` = COALESCE(?, `address`),
        `id_number` = COALESCE(?, `id_number`),
        `email` = COALESCE(?, `email`),
        `position_id` = COALESCE(?, `position_id`),
        `coefficients_salary` = COALESCE(?, `coefficients_salary`),
        `tax` = COALESCE(?, `tax`),
        `communist_party_joined` = COALESCE(?, `communist_party_joined`),
        `unit_id` = COALESCE(?, `unit_id`),
        `day_joined` = COALESCE(?, `day_joined`)
        WHERE `qlcb_uid` = ?');
        $success = $query->execute([
            $full_name,
            $date_of_birth,
            $gender,
            $phone,
            $address,
            $id_number,
            $email,
            $position_id,
            $coefficients_salary,
            $tax,
            $communist_party_joined,
            $unit_id,
            $day_joined,
            $qlcb_uid,
        ]);

        // if ($query->rowCount() > 0) {
        //     return new JSONResponse(["status" => "success"]);
        // } else {
        //     return new JSONResponse(["status" => "error", "message" => "No rows updated. Check if the qlcb_uid is correct or if the data is different from the current values."]);
        // }

        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function deleteUser($qlcb_uid)
    {
        $query = $this->db->getQueryBuilder();
        $query
            ->delete("qlcb_user")
            ->where(
                $query
                    ->expr()
                    ->eq("qlcb_uid", $query->createNamedParameter($qlcb_uid))
            )
            ->execute();

        return new JSONResponse(["status" => "success"]);
    }
}
