<?php
class PostManager {
    private $csvFile;

    public function __construct($csvFile) {
        $this->csvFile = $csvFile;
    }

    public function getPosts() {
        $posts = [];
        if (file_exists($this->csvFile)) {
            $file = fopen($this->csvFile, 'r');
            while (($row = fgetcsv($file)) !== FALSE) {
                $posts[] = $row;
            }
            fclose($file);
        }
        return $posts;
    }
    public function getPostByTitle($title) {
        if (file_exists($this->csvFile)) {
            $file = fopen($this->csvFile, 'r');
            while (($row = fgetcsv($file)) !== FALSE) {
                if ($row[1] == $title) {
                    fclose($file);
                    return $row;
                }
            }
            fclose($file);
        }
        return null;
    }

    public function updatePost($originalTitle, $newTitle, $newContent) {
        if (file_exists($this->csvFile)) {
            $file = fopen($this->csvFile, 'r');
            $tempFile = fopen('temp.csv', 'w');
            while (($row = fgetcsv($file)) !== FALSE) {
                if ($row[1] == $originalTitle) {
                    $row[1] = $newTitle;
                    $row[2] = $newContent;
                }
                fputcsv($tempFile, $row);
            }
            fclose($file);
            fclose($tempFile);
            rename('temp.csv', $this->csvFile);
        }
    }

    public function deletePost($title) {
        if (file_exists($this->csvFile)) {
            $file = fopen($this->csvFile, 'r');
            $tempFile = fopen('temp.csv', 'w');
            while (($row = fgetcsv($file)) !== FALSE) {
                if ($row[1] != $title)
                    fputcsv($tempFile, $row);
            }
            fclose($file);
            fclose($tempFile);
            rename('temp.csv', $this->csvFile);
        }
    }
}
?>
