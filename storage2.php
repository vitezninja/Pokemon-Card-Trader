<?php
interface IFileIO2 {
  function save($data);
  function load();
}
abstract class FileIO2 implements IFileIO2 {
  protected $filepath;

  public function __construct($filename) {
    if (!is_readable($filename) || !is_writable($filename)) {
      throw new Exception("Data source {$filename} is invalid.");
    }
    $this->filepath = realpath($filename);
  }
}
class JsonIO2 extends FileIO2 {
  public function load($assoc = true) {
    $file_content = file_get_contents($this->filepath);
    return json_decode($file_content, $assoc) ?: [];
  }

  public function save($data) {
    $json_content = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($this->filepath, $json_content);
  }
}
class SerializeIO2 extends FileIO2 {
  public function load() {
    $file_content = file_get_contents($this->filepath);
    return unserialize($file_content) ?: [];
  }

  public function save($data) {
    $serialized_content = serialize($data);
    file_put_contents($this->filepath, $serialized_content);
  }
}

interface IStorage2 {
  function add($record): string;
  function findById(string $id);
  function findAll(array $params = []);
  function findOne(array $params = []);
  function update(string $id, $record);
  function delete(string $id);

  function findMany(callable $condition);
  function updateMany(callable $condition, callable $updater);
  function deleteMany(callable $condition);
}

class Storage2 implements IStorage2 {
  protected $contents;
  protected $io;

  public function __construct(IFileIO2 $io, $assoc = true) {
    $this->io = $io;
    $this->contents = (array)$this->io->load($assoc);
  }

  public function __destruct() {
    $this->io->save($this->contents);
  }

  public function add($record): string {
    $id = $record["id"];
    $dataToStore = [];
    $dataToStore["name"] = $record["cardname"];
    $dataToStore["type"] = $record["type"];
    $dataToStore["hp"] = (int)$record["hp"];
    $dataToStore["attack"] = (int)$record["attack"];
    $dataToStore["defense"] = (int)$record["defense"];
    $dataToStore["price"] = (int)$record["price"];
    $dataToStore["description"] = $record["description"];
    $dataToStore["image"] = $record["image"]; 
    $this->contents[$id] = $dataToStore;
    return $id;
  }

  public function findById(string $id) {
    return $this->contents[$id] ?? NULL;
  }

  public function findAll(array $params = []) {
    return array_filter($this->contents, function ($item) use ($params) {
      foreach ($params as $key => $value) {
        if (((array)$item)[$key] !== $value) {
          return FALSE;
        }
      }
      return TRUE;
    });
  }

  public function findOne(array $params = []) {
    $found_items = $this->findAll($params);
    $first_index = array_keys($found_items)[0] ?? NULL;
    return $found_items[$first_index] ?? NULL;
  }

  public function update($id, $record) {
    $this->contents[$id] = $record;
  }

  public function delete($id) {
    unset($this->contents[$id]);
  }

  public function findMany(callable $condition) {
    return array_filter($this->contents, $condition);
  }

  public function findAdminCards(array $params = []) {
    return array_filter($this->contents, function ($key) use ($params) {
      foreach ($params as $value) {
        if ($key === $value) {
          return TRUE;
        }
      }
      return FALSE;
    }, ARRAY_FILTER_USE_KEY);
  }

  public function updateMany(callable $condition, callable $updater) {
    array_walk($all, function (&$item) use ($condition, $updater) {
      if ($condition($item)) {
        $updater($item);
      }
    });
  }

  public function deleteMany(callable $condition) {
    $this->contents = array_filter($this->contents, function ($item) use ($condition) {
      return !$condition($item);
    });
  }
}
