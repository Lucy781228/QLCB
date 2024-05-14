<?php
namespace OCA\QLCB\Controller;

use OCP\IRequest;
use OCP\IDBConnection;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;

class DiplomaController  extends Controller{
    private $db;

    public function __construct($AppName, IRequest $request, IDBConnection $db) {
        parent::__construct($AppName, $request);
        $this->db = $db;
    }


    /**
     * @NoCSRFRequired
     */
    public function getAllDiplomas() {
        $query = $this->db->getQueryBuilder();
        $query->select('*')
            ->from('qlcb_diploma');

        $result = $query->execute();
        $diplomas = $result->fetchAll();
        return ['diplomas' => $diplomas];
    }
	

    /**
     * @NoCSRFRequired
     */
    public function createDiploma($diploma_id, $diploma_name) {
        $query = $this->db->getQueryBuilder();
        $query->insert('qlcb_diploma')
            ->values([
                'diploma_id' => $query->createNamedParameter($diploma_id),
                'diploma_name' => $query->createNamedParameter($diploma_name),
            ])
            ->execute();
        return new DataResponse(['status' => 'success']);
    }


    /**
     * @NoCSRFRequired
     */
    public function deleteDiploma($diploma_id) {
        $query = $this->db->getQueryBuilder();
        $query->delete('qlcb_diploma')
            ->where($query->expr()->eq('diploma_id', $query->createNamedParameter($diploma_id)))
            ->execute();
        return new DataResponse(['status' => 'success']);
    }
}