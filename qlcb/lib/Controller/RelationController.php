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

class RelationController extends Controller
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
    public function createRelation(
        $qlcb_uid,
        $full_name,
        $date_of_birth,
        $phone,
        $address,
        $relationship
    ) {
        $query = $this->db->getQueryBuilder();
        $query
            ->insert("qlcb_relation")
            ->values([
                "qlcb_uid" => $query->createNamedParameter($qlcb_uid),
                "full_name" => $query->createNamedParameter($full_name),
                "date_of_birth" => $query->createNamedParameter($date_of_birth),
                "phone" => $query->createNamedParameter($phone),
                "address" => $query->createNamedParameter($address),
                "relationship" => $query->createNamedParameter($relationship),
            ])
            ->execute();

        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function getAllRelations($qlcb_uid)
    {
        $query = $this->db->getQueryBuilder();

        $query
            ->select("*")
            ->from("qlcb_relation")
            ->where(
                $query
                    ->expr()
                    ->eq("qlcb_uid", $query->createNamedParameter($qlcb_uid))
            );

        $result = $query->execute();
        $relations = $result->fetchAll();
        return ["relations" => $relations];
    }

    /**
     * @NoCSRFRequired
     */
    public function getTypes($qlcb_uid)
    {
        $query = $this->db->getQueryBuilder();

        $query
            ->select("relationship")
            ->from("qlcb_relation")
            ->where(
                $query
                    ->expr()
                    ->eq("qlcb_uid", $query->createNamedParameter($qlcb_uid))
            )
            ->groupBy("relationship");

        $result = $query->execute();
        $types = $result->fetchAll();
        return ["types" => $types];
    }

    /**
     * @NoCSRFRequired
     */
    public function updateRelation(
        $qlcb_uid,
        $full_name,
        $date_of_birth,
        $phone,
        $address,
        $relationship,
        $relation_id
    ) {
        $query = $this->db->prepare('UPDATE `oc_qlcb_relation` SET 
        `full_name` = COALESCE(?, `full_name`), 
        `date_of_birth` = COALESCE(?, `date_of_birth`),
        `phone` = COALESCE(?, `phone`),
        `address` = COALESCE(?, `address`),
        `relationship` = COALESCE(?, `relationship`),
        `qlcb_uid` = COALESCE(?, `qlcb_uid`)
        WHERE `relation_id` = ?');
        $success = $query->execute([
            $full_name,
            $date_of_birth,
            $phone,
            $address,
            $relationship,
            $qlcb_uid,
            $relation_id,
        ]);

        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function deleteRelation($relation_id)
    {
        $query = $this->db->getQueryBuilder();
        $query
            ->delete("qlcb_relation")
            ->where(
                $query
                    ->expr()
                    ->eq(
                        "relation_id",
                        $query->createNamedParameter($relation_id)
                    )
            )
            ->execute();

        return new JSONResponse(["status" => "success"]);
    }
}
