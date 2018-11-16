<?php
/**
 * Created by PhpStorm.
 * User: rigo
 * Date: 11/12/18
 * Time: 9:12 PM
 */
$bucketName = "travel-sample";

// Establish username and password for bucket-access
$authenticator = new \Couchbase\PasswordAuthenticator();
$authenticator->username('Administrator')->password('ogir3590');

// Connect to Couchbase Server
$cluster = new CouchbaseCluster("couchbase://127.0.0.1");

// Authenticate, then open bucket
$cluster->authenticate($authenticator);
$bucket = $cluster->openBucket($bucketName);

// Store a document
echo "Storing u:king_arthur\n";
$result = $bucket->upsert('u:king_arthur', array(
    "email" => "kingarthur@couchbase.com",
    "interests" => array("African Swallows")
));

var_dump($result);

// Retrieve a document
echo "Getting back u:king_arthur\n";
$result = $bucket->get("u:king_arthur");
var_dump($result->value);

// Replace a document
echo "Replacing u:king_arthur\n";
$doc = $result->value;
array_push($doc->interests, 'PHP 7');
$bucket->replace("u:king_arthur", $doc);
var_dump($result);

echo "Creating primary index\n";
// Before issuing a N1QL Query, ensure that there is
// is actually a primary index.
try {
    // Do not override default name; fail if it already exists, and wait for completion
    $bucket->manager()->createN1qlPrimaryIndex('', false, false);
    echo "Primary index has been created\n";
} catch (CouchbaseException $e) {
    printf("Couldn't create index. Maybe it already exists? (code: %d)\n", $e->getCode());
}

// Query with parameters
$query = CouchbaseN1qlQuery::fromString("SELECT * FROM `$bucketName` WHERE \$p IN interests");
$query->namedParams(array("p" => "African Swallows"));
echo "Parameterized query:\n";
var_dump($query);
$rows = $bucket->query($query);
echo "Results:\n";
var_dump($rows);