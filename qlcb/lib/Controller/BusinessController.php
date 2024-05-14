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

class BusinessController extends Controller
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
    public function createBusiness(
        $qlcb_uid,
        $start_date,
        $end_date,
        $unit,
        $position
    ) {
        $query = $this->db->getQueryBuilder();
        $query
            ->insert("qlcb_business")
            ->values([
                "qlcb_uid" => $query->createNamedParameter($qlcb_uid),
                "start_date" => $query->createNamedParameter($start_date),
                "end_date" => $query->createNamedParameter($end_date),
                "unit" => $query->createNamedParameter($unit),
                "position" => $query->createNamedParameter($position),
            ])
            ->execute();

        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function getBusinesses($qlcb_uid)
    {
        $query = $this->db->getQueryBuilder();

        $query
            ->select("*")
            ->from("qlcb_business")
            ->where(
                $query
                    ->expr()
                    ->eq("qlcb_uid", $query->createNamedParameter($qlcb_uid))
            );

        $result = $query->execute();
        $businesses = $result->fetchAll();
        return ["businesses" => $businesses];
    }

    /**
     * @NoCSRFRequired
     */
    public function getAllBusinesses()
    {
        $query = $this->db->getQueryBuilder();

        $query
            ->select("e.*", "u.full_name")
            ->from("qlcb_business", "e")
            ->leftJoin("e", "qlcb_user", "u", "e.qlcb_uid = u.qlcb_uid");

        $result = $query->execute();
        $businesses = $result->fetchAll();
        return ["businesses" => $businesses];
    }

    /**
     * @NoCSRFRequired
     */
    public function updateBusiness(
        $qlcb_uid,
        $start_date,
        $end_date,
        $unit,
        $position,
        $business_id
    ) {
        $query = $this->db->prepare('UPDATE `oc_qlcb_business` SET 
        `start_date` = COALESCE(?, `start_date`), 
        `end_date` = COALESCE(?, `end_date`),
        `unit` = COALESCE(?, `unit`),
        `position` = COALESCE(?, `position`),
        `qlcb_uid` = COALESCE(?, `qlcb_uid`)
        WHERE `business_id` = ?');
        $success = $query->execute([
            $start_date,
            $end_date,
            $unit,
            $position,
            $qlcb_uid,
            $business_id,
        ]);

        return new JSONResponse(["status" => "success"]);
    }

    /**
     * @NoCSRFRequired
     */
    public function deleteBusiness($business_id)
    {
        $query = $this->db->getQueryBuilder();
        $query
            ->delete("qlcb_business")
            ->where(
                $query
                    ->expr()
                    ->eq(
                        "business_id",
                        $query->createNamedParameter($business_id)
                    )
            )
            ->execute();

        return new JSONResponse(["status" => "success"]);
    }
}
