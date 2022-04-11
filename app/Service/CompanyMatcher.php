<?php

namespace App\Service;

class CompanyMatcher
{
    private $db;
    private $matches = [];

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function match()
    {
        // save server overhead - done in query
    }

    public function pick(int $count)
    {
        // save server overhead - done in query
    }

    public function results(int $i): array
    {
        
        $regex = "/[^A-Za-z ]/"; // ignore letters and spaces 
        $p = preg_split($regex,  $_POST["postcode"]); // split off all the numbers
        $postcode = '["' . $p[0] . '"]'; // grab the first group of letters

        $bedrooms = '"' . $_POST["bedrooms"] . '"';
        $type = $_POST["type"];

        $q = "SELECT * 
            FROM companies 
            WHERE ( credits > 0 AND id IN (SELECT company_id 
                FROM company_matching_settings 
                WHERE (postcodes = '" . $postcode . "' AND bedrooms LIKE '%" . $bedrooms . "%' AND type = '" . $type . "')))
            ORDER BY RAND() LIMIT " . $i;

        echo ($q);
        echo ('</BR>');

        $query = $this->db->prepare($q);
        $query->execute();
        $matches = $query->fetchAll();
        
        return $matches;

    }

    public function deductCredits($list)
    {
        foreach ($list as $row) {
            $row['credits'] = $row['credits'] - 1;
            $u = "UPDATE companies SET credits = " . $row['credits'] . " WHERE id = " . $row['id'] . ";";
            // $update = $this->db->query($u);

            //log in file
            if (strval($row['credits']) == 0) {
                $myfile = fopen("outofcredit.txt", "a") or die("Unable to open file!");
                $txt = date("Y-m-d") . " " . $row['name'] . " credit " . $row['credits'] . "\n";
                fwrite($myfile, $txt);
                fclose($myfile);
            }
        }
    }
}
