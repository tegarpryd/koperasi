<?php

require_once 'Model.php';

class StaticPage extends Model
{
    protected $table = 'static_pages';

    public function findAll()
    {
        $stmt = $this->db->query("SELECT id, title, slug, is_published, updated_at FROM {$this->table} ORDER BY title ASC");
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (title, slug, body, is_published) VALUES (:title, :slug, :body, :is_published)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':body' => $data['body'],
            ':is_published' => $data['is_published'] ?? 0
        ]);
    }

    public function update($id, array $data)
    {
        $sql = "UPDATE {$this->table} SET title = :title, slug = :slug, body = :body, is_published = :is_published WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':body' => $data['body'],
            ':is_published' => $data['is_published'] ?? 0,
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
