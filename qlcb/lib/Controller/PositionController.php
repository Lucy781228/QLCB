<?php
namespace OCA\QLCB\Controller;

use OCP\IRequest;
use OCP\IDBConnection;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;

class PositionController  extends Controller{
    private $db;

    public function __construct($AppName, IRequest $request, IDBConnection $db) {
        parent::__construct($AppName, $request);
        $this->db = $db;
    }


    /**
     * @NoCSRFRequired
     */
    public function getAllPositions() {
        $query = $this->db->getQueryBuilder();
        $query->select('*')
            ->from('qlcb_position');

        $result = $query->execute();
        $positions = $result->fetchAll();
        return ['positions' => $positions];
    }
	

    /**
     * @NoCSRFRequired
     */
    public function createPosition($position_id, $position_name) {
        $query = $this->db->getQueryBuilder();
        $query->insert('qlcb_position')
            ->values([
                'position_id' => $query->createNamedParameter($position_id),
                'position_name' => $query->createNamedParameter($position_name),
            ])
            ->execute();
        return new DataResponse(['status' => 'success']);
    }


    /**
     * @NoCSRFRequired
     */
    public function deletePosition($position_id) {
        $query = $this->db->getQueryBuilder();
        $query->delete('qlcb_position')
            ->where($query->expr()->eq('position_id', $query->createNamedParameter($position_id)))
            ->execute();
        return new DataResponse(['status' => 'success']);
    }
}