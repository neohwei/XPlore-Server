<?php

/*
 * Copyright 2015 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Cloud\Samples\Bookshelf\DataModel;

/**
 * The common model implemented by Google Datastore, mysql, etc.
 */
interface DataModelInterface
{
    /**
     * Lists all the books in the data model.
     * Cannot simply be called 'list' due to PHP keyword collision.
     *
     * @param int  $limit  How many books will we fetch at most?
     * @param null $cursor Returned by an earlier call to listBooks().
     *
     * @return array ['books' => array of associative arrays mapping column
     *               name to column value,
     *               'cursor' => pass to next call to listBooks() to fetch
     *               more books]
     */
    public function listBooks($limit = 10, $cursor = null);

    /**
     * Creates a new book in the data model.
     *
     * @param $book array  An associative array.
     * @param null $id integer  The id, if known.
     *
     * @return mixed The id of the new book.
     */
    public function create($book, $id = null);

    /**
     * Reads a book from the data model.
     *
     * @param $id  The id of the book to read.
     *
     * @return mixed An associative array representing the book if found.
     *               Otherwise, a false value.
     */
    public function read($id);

    /**
     * Updates a book in the data model.
     *
     * @param $book array  An associative array representing the book.
     * @param null $id The old id of the book.
     *
     * @return int The number of books updated.
     */
    public function update($book);

    /**
     * Deletes a book from the data model.
     *
     * @param $id  The book id.
     *
     * @return int The number of books deleted.
     */
    public function delete($id);
    
    
    /*
    //Checking if user is authorized for access of information
    public function accessCheck($user_uid, $token);
    
    //Register user and store user into data model
    public function register($id, $name, $email, $password);
    
    //Login user by checking id or email and password
    public function login($id, $password);
    
    //Logout user and set GCM id to null
    public function logout($user_uid);
    
    //Check if this email has been used by another user
    public function isExistingEmail($email);
    
    //Check if this id has been taken
    public function isExistingID($id);
    
    //Get user posts that is within a specific radius
    public function getNearbyPosts($latitude, $longitude, $user_uid, $cursor = null);
    
    //Get nearby events within radius
    public function getNearbyEvents($latitude, $longitude, $user_uid, $cursor = null);
    
    //Get nearby pages (Could be Shops, Restaurants and more) within radius
    public function getNearbyPages($latitude, $longitude, $user_uid, $cursor = null);
    
    //Get users that were nearby not long ago
    public function getNearbyUsers($lat, $lng, $user_uid, $cursor = null);
    
    //Get events posted by a user
    public function getUserEvents($user_uid, $search_user_uid, $cursor = null);
    
    //Get pages that were posted by a the user
    public function getUserPages($user_uid, $search_user_uid, $cursor = null);
    
    //Get posts posted by this user
    public function getUserPosts($user_uid, $search_user_uid, $cursor = null);
    
    //Get user flags
    public function getUserFlags($user_uid, $search_user_uid, $cursor = null);
    
    //Get page postses that were posted by this page
    public function getPagePosts($page_uid, $user_uid, $cursor = null);
    
    //Get friends of this user
    public function getUserFriends($user_uid, $status, $cursor = null);
    
    //Get notifications that belongs to this user
    public function getUserNotifications($user_uid, $cursor = null);
    
    //Storing new notification to data model and at the same time send notification to the targeted users
    public function storeNotification($user_uid, $receiving_user_uid, $type, $content_type, $post_uid = "NULL");
    
    //Get details of a notification by its ID
    public function getNotificationDetails($notification_uid);
    
    //Send notification to targeted user
    public function sendNotification($notification, $user_uid);
    
    //Send friend request to a user and storing it in data model
    public function sendFriendRequest($from_user_uid, $to_user_uid);
    
    //Getting the friend posts between two user (Friend, Pending, Accepting)
    public function getFriendStatus($user_uid1, $user_uid2);
    
    //Get follow page posts (Following, Not following)
    public function getFollowPageStatus($user_uid, $page_uid);
    
    //Accept a friend request from another user and updating the data model
    public function acceptFriendRequest($from_user_uid, $to_user_uid);
    
    //Removing a sent request by removing its data from data model
    public function removeFriendRequest($from_user_uid, $to_user_uid);
    
    //Stores follow page posts to data model
    public function followPage($user_uid, $page_uid);
    
    //Removes follow page posts from data model
    public function unfollowPage($user_uid, $page_uid);
    
    //Get user profile details according to user_uid
    public function getUserProfile($user_uid);
    
    //Update user profile details with or without a new profile image url
    public function updateUserProfile($user_uid, $name, $email, $profile_image = "NULL");
    
    //Check if current password matches and update user password
    public function changeUserPassword($user_uid, $current_password, $new_password);
    
    //Update user's Firebase GCM id for push notifications
    public function updateUserGCM($user_uid, $firebase_id);
    
    //Update user's last known location
    public function updateUserLocation($user_uid, $lat, $lng);
    
    //Update user's temporary token
    public function updateUserToken($user_uid);
    
    //Get page details according to page_uid
    public function getPageDetails($page_uid, $user_uid);
    
    //Get details of a post
    public function getPostDetails($post_uid, $user_uid);
    
    //Get flag details
    public function getFlagDetails($flag_uid);
    
    //Get images that belongs to a post
    public function getPostImages($post_uid, $tag, $cursor = null);
    
    //Get likes on a post
    public function getPostLikes($post_uid, $user_uid, $tag, $cursor = null);
    
    //Get comments on a post
    public function getPostComments($post_uid, $user_uid, $tag, $cursor = null);
    
    //Get details of an event
    public function getEventDetails($event_uid, $user_uid);
    
    //Posting a new status
    public function newPost($user_uid, $description, $lat, $lng, $image_count, $address, $visibility, $location_type, $post_type);
    
    //Deleting a post
    public function deletePost($post_uid, $user_uid);
    
    //New Flag
    public function newFlag($user_uid, $description, $lat, $lng);
    
     //Deleting a post
    public function deleteFlag($flag_uid);
    
    //Posting a new event
    public function newEvent($user_uid, $poster_uid, $title, $description, $start_date, $end_date, $start_time, $end_time, $lat, $lng, $image_count, $address, $visibility, $organiser_type);
    
    //Deleting an event
    public function deleteEvent($event_uid);
    
    //Uploading a new page to user profile
    public function newPage($user_uid, $title, $description, $lat, $lng, $profile_image_url, $address);
    
    //Deleting a page
    public function deletePage($page_uid);
    
    //Editing a page
    public function editPage($page_uid, $user_uid, $title, $description, $lat, $lng, $profile_image_url, $address);
    
    //Attaching a new image to a post
    public function addImageToPost($post_uid, $image_url, $description, $image_type, $width, $height, $tag);
    
    //Removing an image from a post
    public function removeImageFromPost($image_uid);
    
    //Increasing post like count
    public function incrementPostLikeCount($post_uid);
    
     //Decreasing post like count
    public function decrementPostLikeCount($post_uid);
    
    //Increasing post comment count
    public function incrementPostCommentCount($post_uid);
    
    //Increasing event like count
    public function incrementEventLikeCount($event_uid);
    
    //Increasing event comment count
    public function incrementEventCommentCount($event_uid);
    
     //Decreasing event like count
    public function decrementEventLikeCount($event_uid);
    
    //Increasing user post count
    public function incrementUserPostCount($user_uid);
    
    //Decreasing user post count
    public function decrementUserPostCount($user_uid);
    
    //Increasing page post count
    public function incrementPagePostCount($page_uid);
    
     //Decreasing page post count
    public function decrementPagePostCount($page_uid);
    
    //Increasing user friend count
    public function incrementUserFriendCount($user_uid);
    
    //Decreasing user friend count
    public function decrementUserFriendCount($user_uid);
    
    //Increasing page follower count
    public function incrementPageFollowerCount($page_uid);
    
     //Decreasing page follower count
    public function decrementPageFollowerCount($page_uid);
    
    //Listing the matching users that matches the keyword
    public function searchUsers($keyword, $user_uid, $cursor = null);
    
    //Listing the matching pages that matches the keyword
    public function searchPages($keyword, $user_uid, $cursor = null);
    
    //Posting a like to a post or event
    public function postLike($user_uid, $post_uid, $tag);
    
    //Removing a like posted on a post or event
    public function postUnlike($user_uid, $post_uid, $tag);
    
    //Posting a comment to a post or event
    public function postComment($user_uid, $post_uid, $description, $tag);
    
    //Check if this post or event is liked by this user
    public function isUserLikedPost($post_uid, $user_uid, $tag);
    
    //Check if user responded to an event
    public function isUserAttendedEvent($event_uid, $user_uid);
    
    //Adding an attender that responded to an event
    public function addEventAttender($event_uid, $user_uid, $attender_status);
    
    //Updating the respond of an attender
    public function updateEventAttender($event_uid, $user_uid, $attender_status);
    
    //Getting users that responded on an event
    public function getEventAttenders($event_uid, $cursor = null);
    
    //Getting users with specific respond on an event (Maybe, Going, Interested)
    public function getSpecificEventAttenders($event_uid, $attender_status, $user_uid, $cursor = null);
   
   	//Generating a token
   	public function generateToken($prefix);
   	
   	//Generating a unique ID
   	public function generateUID($prefix);
   	
   	//Encrypting a password
   	public function hashSSHA($password);
   	
   	*/
   
    
}
