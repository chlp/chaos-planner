<?php

require __DIR__ . '/../vendor/autoload.php';

use Google\Cloud\Firestore\FirestoreClient;

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/../gcloud-service-key.json');
$firestore = new FirestoreClient([
    'projectId' => 'sunny-buttress-438111-g7',
]);

$collectionName = 'users';

function addUser($firestore, $collectionName, $userId, $userData)
{
    $collection = $firestore->collection($collectionName);
    $document = $collection->document($userId);
    $document->set($userData);
    echo "User added successfully.\n";
}

function getUser($firestore, $collectionName, $userId)
{
    $collection = $firestore->collection($collectionName);
    $document = $collection->document($userId);
    $snapshot = $document->snapshot();

    if ($snapshot->exists()) {
        printf("User: %s\n", $snapshot->id());
        print_r($snapshot->data());
    } else {
        echo "User not found.\n";
    }
}

function updateUser($firestore, $collectionName, $userId, $newData)
{
    $collection = $firestore->collection($collectionName);
    $document = $collection->document($userId);
    $document->update($newData);
    echo "User updated successfully.\n";
}

function deleteUser($firestore, $collectionName, $userId)
{
    $collection = $firestore->collection($collectionName);
    $document = $collection->document($userId);
    $document->delete();
    echo "User deleted successfully.\n";
}

$userId = 'user1';
$userData = [
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'age' => 30
];

addUser($firestore, $collectionName, $userId, $userData);
$userId = 'user2';
$userData['name'] = 'Jane Doe2';

addUser($firestore, $collectionName, $userId, $userData);

$userId = 'user3';
$userData['name'] = 'Jane Doe3';
$userData['email'] = 'a@b.com';
addUser($firestore, $collectionName, $userId, $userData);

getUser($firestore, $collectionName, $userId);

$newData = [
    ['path' => 'age', 'value' => 31],
    ['path' => 'email', 'value' => 'new.email@example.com']
];
updateUser($firestore, $collectionName, $userId, $newData);

//deleteUser($firestore, $collectionName, $userId);