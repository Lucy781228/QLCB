<?php
namespace OCA\QLCB\Controller;

use OCP\IRequest;
use OCP\IDBConnection;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;

class UnitController  extends Controller{
    private $db;

    public function __construct($AppName, IRequest $request, IDBConnection $db) {
        parent::__construct($AppName, $request);
        $this->db = $db;
    }


    /**
     * @NoCSRFRequired
     */
    public function getAllUnits() {
        $query = $this->db->getQueryBuilder();
        $query->select('*')
            ->from('qlcb_unit');

        $result = $query->execute();
        $units = $result->fetchAll();
        return ['units' => $units];
    }
	

    /**
     * @NoCSRFRequired
     */
    public function createUnit($unit_id, $unit_name) {
        $query = $this->db->getQueryBuilder();
        $query->insert('qlcb_unit')
            ->values([
                'unit_id' => $query->createNamedParameter($unit_id),
                'unit_name' => $query->createNamedParameter($unit_name),
            ])
            ->execute();
        return new DataResponse(['status' => 'success']);
    }


    /**
     * @NoCSRFRequired
     */
    public function deleteUnit($unit_id) {
        $query = $this->db->getQueryBuilder();
        $query->delete('qlcb_unit')
            ->where($query->expr()->eq('unit_id', $query->createNamedParameter($unit_id)))
            ->execute();
        return new DataResponse(['status' => 'success']);
    }
}