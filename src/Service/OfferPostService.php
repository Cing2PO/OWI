<?php
require_once "../Model/Database.php";
class OfferPostService
{
    private $db;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    public function getPostByHighestUpvote(): array
    {
        $query = "SELECT penawaran_donasi.id, penawaran_donasi.judul, penawaran_donasi.foto, penawaran_donasi.dibuat_pada, user.nama_depan, user.nama_belakang FROM penawaran_donasi JOIN user ON penawaran_donasi.nik_pembuat = user.nik ORDER BY dibuat_pada DESC LIMIT 5";
        $statement = $this->db->prepare($query);
        $statement->execute();
        $result = $statement->get_result();
        if (!empty($result)) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    public function getAllOfferPost($page): ?array
    {
        $post_per_page = 10;
        $offset = ($page) * $post_per_page;
        $query = "SELECT penawaran_donasi.id, penawaran_donasi.judul, penawaran_donasi.foto, penawaran_donasi.dibuat_pada, user.nama_depan, user.nama_belakang FROM penawaran_donasi JOIN user ON penawaran_donasi.nik_pembuat = user.nik LIMIT ? OFFSET ? ";
        $statement = $this->db->prepare($query);
        $statement->bind_param("ii", $post_per_page, $offset);
        $statement->execute();
        $result = $statement->get_result();
        if (!empty($result)) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return null;
    }
    public function getOfferPostDetailsById(int $id): ?array
    {
        $query = "SELECT id, judul, deskripsi, foto, nik_pembuat, id_alamat, dibuat_pada FROM penawaran_donasi WHERE id = ?;";
        $statement = $this->db->prepare($query);
        $statement->bind_param("i", $id);
        $statement->execute();
        $result = $statement->get_result();
        if(!empty($result)){
            return $result->fetch_assoc();
        }
        return null;
    }
    public function createOfferingPost($judul, $deskripsi, $id_alamat, $foto, $nik_pembuat) : ?int
    {
        $query = "INSERT INTO penawaran_donasi(judul, deskripsi, dibuat_pada, id_alamat, foto, nik_pembuat) VALUES (?, ?, ?, ?, ?, ?)";
        $statement = $this->db->prepare($query);
        $now = date("Y-m-d H:i:s");
        $statement->bind_param("sssisi", $judul, $deskripsi, $now, $id_alamat, $foto, $nik_pembuat );
        if($statement->execute()){
            return $statement->insert_id;
        }
        return null;
    }
}