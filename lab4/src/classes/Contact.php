<?php

namespace CT275\Labs;

use PDO;

class Contact
{
    private ?PDO $db;

    private int $id = -1;
    public $name;
    public $phone;
    public $notes;
    public $created_at;
    public $updated_at;
    ### Thêm cột avatar nên thêm dòng bên dưới vào
    public $avatar;
    private array $errors = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function __construct(?PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function fill(array $data): Contact
    {
        $this->name = $data['name'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->notes = $data['notes'] ?? '';
        $this->avatar = $data['avatar'] ?? ''; // Thêm avatar vào fill
        return $this;
    }


    public function getValidationErrors(): array
    {
        return $this->errors;
    }

    public function validate(): bool
    {
        $name = trim($this->name);
        if (!$name) {
            $this->errors['name'] = 'Invalid name.';
        }

        $validPhone = preg_match(
            '/^(03|05|07|08|09|01[2|6|8|9])+([0-9]{8})\b$/',
            $this->phone
        );
        if (!$validPhone) {
            $this->errors['phone'] = 'Invalid phone number.';
        }

        $notes = trim($this->notes);
        if (strlen($notes) > 255) {
            $this->errors['notes'] = 'Notes must be at most 255 characters.';
        }

        return empty($this->errors);
    }

    public function all(): array
    {
        $contacts = [];
        $statement = $this->db->prepare('select * from contacts');
        $statement->execute();
        while ($row = $statement->fetch()) {
            $contact = new Contact($this->db);
            $contact->fillFromDB($row);
            $contacts[] = $contact;
        }
        return $contacts;
    }
    protected function fillFromDB(array $row): Contact
    {
        [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'avatar' => $this->avatar, // Thêm avatar vào fillFromDB
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ] = $row;
        return $this;
    }

    public function count(): int
    {
        $statement = $this->db->prepare('select count(*) from contacts');
        $statement->execute();
        return $statement->fetchColumn();
    }
    public function paginate(int $offset = 0, int $limit = 10): array
    {
        $contacts = [];
        $statement = $this->db->prepare('select * from contacts limit :offset,
:limit');

        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        while ($row = $statement->fetch()) {
            $contact = new Contact($this->db);
            $contact->fillFromDB($row);
            $contacts[] = $contact;
        }
        return $contacts;
    }

    public function save(): bool
    {
        $result = false;
        if ($this->id >= 0) {
            $statement = $this->db->prepare(
                'update contacts set name = :name,
                phone = :phone, notes = :notes, avatar = :avatar, updated_at = now()
                where id = :id'
            );
            $result = $statement->execute([
                'name' => $this->name,
                'phone' => $this->phone,
                'notes' => $this->notes,
                'avatar' => $this->avatar, // Thêm avatar vào câu lệnh SQL
                'id' => $this->id
            ]);
        } else {
            $statement = $this->db->prepare(
                'insert into contacts (name, phone, notes, avatar, created_at, updated_at)
                values (:name, :phone, :notes, :avatar, now(), now())'
            );
            $result = $statement->execute([
                'name' => $this->name,
                'phone' => $this->phone,
                'notes' => $this->notes,
                'avatar' => $this->avatar // Thêm avatar vào câu lệnh SQL

            ]);
            if ($result) {
                $this->id = $this->db->lastInsertId();
            }
        }
        return $result;
    }

    public function find(int $id): ?Contact
    {
        $statement = $this->db->prepare('select * from contacts where id = :id');
        $statement->execute(['id' => $id]);
        if ($row = $statement->fetch()) {
            $this->fillFromDB($row);
            return $this;
        }
        return null;
    }
    public function update(array $data): bool
    {
        $this->fill($data);
        if ($this->validate()) {
            return $this->save();
        }
        return false;
    }

    public function delete(): bool
    {
        $statement = $this->db->prepare('delete from contacts where id = :id');
        return $statement->execute(['id' => $this->id]);
    }
}
