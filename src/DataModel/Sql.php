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
use Google\Cloud\Samples\Bookshelf\Firebase\Firebase;
require_once  __DIR__ . '/DataModelInterface.php';
require_once  __DIR__ . '/RequestReplyProcessor.php';

use PDO;

/**
 * Class Sql implements the DataModelInterface with a mysql or postgres database.
 *
 */
class Sql implements DataModelInterface
{
    private $dsn;
    private $user;
    private $password;
    private $limit = 10;
    
    private $rp;
    
    /*
     *  To create table that stores utf8mb4 string,
     *  ALTER TABLE
	 *  table_name
	 *  CONVERT TO CHARACTER SET utf8mb4
	 *  COLLATE utf8mb4_unicode_ci;
     */

    /**
     * Creates the SQL books table if it doesn't already exist.
     */
    public function __construct($dsn, $user, $password)
    {
 	
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
        
        $this->rp = new RequestReplyProcessor();

        $columns = array(
            'id serial PRIMARY KEY ',
            'title VARCHAR(255)',
            'author VARCHAR(255)',
            'published_date VARCHAR(255)',
            'image_url VARCHAR(255)',
            'description VARCHAR(255)',
            'created_by VARCHAR(255)',
            'created_by_id VARCHAR(255)',
        );
        
        $users_column = array(
		     'user_uid SERIAL PRIMARY KEY ',
		     'id VARCHAR(50) unique not null ',
		 	 'token VARCHAR(100) unique not null ',
		 	 'name VARCHAR(100) not null ',
			 'email VARCHAR(100) not null ', 
			 'password VARCHAR(100) not null ',
			 'salt TEXT not null ',
			 'registered_at DATETIME DEFAULT CURRENT_TIMESTAMP ',
			 'updated_at DATETIME null ',
			 'profile_image TEXT null ',
			 'profile_image_small TEXT null',
			 'gcm_id TEXT null ',
			 'post_count INTEGER DEFAULT 0 not null ',
			 'friend_count INTEGER DEFAULT 0 not null ', 
			 'last_latitude DOUBLE null ',
			 'last_longitude DOUBLE null ',
			 'last_location_datetime DATETIME null',
			 'last_fame_update DATETIME DEFAULT CURRENT_TIMESTAMP',
			 'deleted BOOLEAN DEFAULT false',
		 );
    
    	 $pages_column = array(
	    	 'page_uid SERIAL PRIMARY KEY ',
			 'title VARCHAR(100) not null ',
			 'description TEXT null ',
			 'created_at DATETIME DEFAULT CURRENT_TIMESTAMP ',
			 'user_uid VARCHAR(100) not null ',
			 'latitude DOUBLE null ',
			 'longitude DOUBLE null ',
			 'address TEXT null ',
			 'profile_image TEXT ',
			 'profile_image_small TEXT null',
			 'banner_images TEXT null',
			 'post_count INTEGER DEFAULT 0 not null ',
			 'follower_count INTEGER DEFAULT 0 not null',
			 'fame_count INTEGER DEFAULT 0 not null',
			 'checkin_count INTEGER DEFAULT 0 not null',
			 'category VARCHAR(20) null',
			 'editable_by_others BOOLEAN DEFAULT false',
			 'deleted BOOLEAN DEFAULT false',
    	);
    	
    	$page_category_column = array(
	    	 'category_uid SERIAL PRIMARY KEY ',
			 'name VARCHAR(100) not null ',
			 'created_at DATETIME DEFAULT CURRENT_TIMESTAMP ',
			 'user_uid VARCHAR(20) not null',
    	);
    
    	 $posts_column = array(
    		 'post_uid SERIAL PRIMARY KEY ',
			 'description TEXT not null',
			 'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
			 'latitude DOUBLE null',
			 'longitude DOUBLE null',
			 'address TEXT null',
			 'like_count INTEGER DEFAULT 0',
			 'comment_count INTEGER DEFAULT 0',
			 'image_count INTEGER DEFAULT 0',
			 "type ENUM('user', 'page')",
			 "visibility ENUM('public', 'private')",
			 "location_type ENUM('show', 'hide')",
			 'user_uid VARCHAR(20) not null',
			 "checkin_page_uid VARCHAR(100) DEFAULT 'NULL'",
			 'deleted BOOLEAN DEFAULT false',
    	);
    
    	 $events_column = array(
    		 'event_uid SERIAL PRIMARY KEY ',
			 'title VARCHAR(100) not null',
			 'description TEXT not null', 
			 'created_at DATETIME DEFAULT CURRENT_TIMESTAMP', 
			 'start_date DATETIME not null',
			 'end_date DATETIME not null',
			 'start_time VARCHAR(20) not null',
			 'end_time VARCHAR(20) not null',
			 'latitude DOUBLE not null',
			 'longitude DOUBLE not null',
			 'address TEXT not null',
			 'like_count INTEGER DEFAULT 0',
			 'comment_count INTEGER DEFAULT 0',
			 'organiser_uid VARCHAR(20) not null',
			 "organiser_type ENUM('user', 'page')",
			 'image_count INTEGER DEFAULT 0',
			 "visibility ENUM('public', 'private')",
			 "checkin_page_uid VARCHAR(100) DEFAULT 'NULL'",
			 'deleted BOOLEAN DEFAULT false',
    	);
    
    	 $event_attenders_column = array(
    		 'attender_uid SERIAL PRIMARY KEY ',
			 'user_uid VARCHAR(100) not null',
			 'event_uid VARCHAR(100) not null',
			 'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
			 "status ENUM('interested', 'going', 'maybe', 'not going')",
    	);
    
    	 $images_column = array(
    		 'image_uid SERIAL PRIMARY KEY ',
			 'description TEXT null',
			 'path TEXT not null',
			 'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
			 'post_uid VARCHAR(100) not null',
			 "type ENUM('image', 'video')",
			 'height INTEGER DEFAULT 0',
			 'width INTEGER DEFAULT 0',
			 "tag ENUM('post', 'event', 'page', 'flag')",
    	);
    
     	 $likes_column = array(
    		 'like_uid SERIAL PRIMARY KEY ',
			 'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
			 'user_uid VARCHAR(20) not null',
			 'post_uid VARCHAR(100) not null',
			 "tag ENUM('post', 'event', 'page')",
    	);
    
     	 $comments_column = array(
    		 'comment_uid SERIAL PRIMARY KEY ',
			 'description TEXT not null',
			 'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
			 'user_uid VARCHAR(100) not null',
			 'post_uid VARCHAR(100) not null',
			 "tag ENUM('post', 'event', 'page')",
    	);
    
     	 $requests_column = array(
	    	 'request_uid SERIAL PRIMARY KEY ',
			 'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
			 'from_user_uid VARCHAR(100) not null',
			 'to_user_uid VARCHAR(100) not null',
			 "status ENUM('pending', 'accepted', 'normal') default 'normal'",
			 "type ENUM('user', 'page')",
    	);
    
       	$notifications_column = array(
    		 'notification_uid SERIAL PRIMARY KEY ',
			 'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
			 "type ENUM('like', 'comment', 'request received', 'request accepted', 'new follower', 'nearby post', 'nearby page')",
			 "content_type ENUM('user', 'page', 'event')",
			 'content_user_uid VARCHAR(100) not null',
			 'user_uid VARCHAR(20) not null',
			 'post_uid VARCHAR(100) null',
			 "tag ENUM('post', 'event', 'page', 'NULL')",
    	);
    	
    	$flags_column = array(
    		'flag_uid SERIAL PRIMARY KEY',
    		'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
    		'description TEXT',
    		'latitude DOUBLE',
    		'longitude DOUBLE',
    		'user_uid VARCHAR(100) not null',
    		"checkin_page_uid VARCHAR(100) DEFAULT 'NULL'",
    		"address TEXT NULL",
    		'deleted BOOLEAN DEFAULT false',
    	);
    	
    	$reports_column = array(
    		'report_uid SERIAL PRIMARY KEY',
    		'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
    		"tag ENUM('user', 'post', 'event', 'page')",
    		'description TEXT',
    		"category ENUM('inappropriate', 'fake', 'other')",
    		'user_uid VARCHAR(20) not null',
    		'post_uid VARCHAR(100) not null',
    	);

        $pdo = $this->newConnection();
        $this->columnBookNames = array_map(function ($columnBookDefinition) {
            return explode(' ', $columnBookDefinition)[0];
        }, $columns);
        $columnText = implode(', ', $columns);
        $pdo->query("CREATE TABLE IF NOT EXISTS books ($columnText)");
        
        
        $this->columnUserNames = array_map(function ($columnUserDefinition) {
            return explode(' ', $columnUserDefinition)[0];
        }, $users_column);
        $columnText = implode(', ', $users_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS Users ($columnText)");
        
        
        $this->columnPageNames = array_map(function ($columnPageDefinition) {
            return explode(' ', $columnPageDefinition)[0];
        }, $pages_column);
        $columnText = implode(', ', $pages_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS Pages ($columnText)");
        
        
        $this->columnEventNames = array_map(function ($columnEventDefinition) {
            return explode(' ', $columnEventDefinition)[0];
        }, $events_column);
        $columnText = implode(', ', $events_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS Events ($columnText)");
        
        
        $this->columnPostNames = array_map(function ($columnPostDefinition) {
            return explode(' ', $columnPostDefinition)[0];
        }, $posts_column);
        $columnText = implode(', ', $posts_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS Posts ($columnText)");
        
        
        $this->columnLikeNames = array_map(function ($columnLikeDefinition) {
            return explode(' ', $columnLikeDefinition)[0];
        }, $likes_column);
        $columnText = implode(', ', $likes_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS Likes ($columnText)");
        
        
        $this->columnCommentNames = array_map(function ($columnCommentDefinition) {
            return explode(' ', $columnCommentDefinition)[0];
        }, $comments_column);
        $columnText = implode(', ', $comments_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS Comments ($columnText)");
        
        
        $this->columnAttenderNames = array_map(function ($columnAttenderDefinition) {
            return explode(' ', $columnAttenderDefinition)[0];
        }, $event_attenders_column);
        $columnText = implode(', ', $event_attenders_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS EventAttenders ($columnText)");
        
        
        $this->columnImageNames = array_map(function ($columnImageDefinition) {
            return explode(' ', $columnImageDefinition)[0];
        }, $images_column);
        $columnText = implode(', ', $images_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS Images ($columnText)");
        
        
        $this->columnRequestNames = array_map(function ($columnRequestDefinition) {
            return explode(' ', $columnRequestDefinition)[0];
        }, $requests_column);
        $columnText = implode(', ', $requests_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS Requests ($columnText)");
        
        
        $this->columnNotificationNames = array_map(function ($columnNotificationDefinition) {
            return explode(' ', $columnNotificationDefinition)[0];
        }, $notifications_column);
        $columnText = implode(', ', $notifications_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS Notifications ($columnText)");
        
        $this->columnFlagNames = array_map(function ($columnFlagDefinition) {
            return explode(' ', $columnFlagDefinition)[0];
        }, $flags_column);
        $columnText = implode(', ', $flags_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS Flags ($columnText)");
        
        $this->columnReportNames = array_map(function ($columnReportDefinition) {
            return explode(' ', $columnReportDefinition)[0];
        }, $reports_column);
        $columnText = implode(', ', $reports_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS Reports ($columnText)");
        
        $this->columnPageCategoryNames = array_map(function ($columnPageCategoryDefinition) {
            return explode(' ', $columnPageCategoryDefinition)[0];
        }, $page_category_column);
        $columnText = implode(', ', $page_category_column);
        $pdo->query("CREATE TABLE IF NOT EXISTS PageCategories ($columnText)");
    }

    /**
     * Creates a new PDO instance and sets error mode to exception.
     *
     * @return PDO
     */
    private function newConnection()
    {
        $pdo = new PDO($this->dsn, $this->user, $this->password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("set names utf8mb4");

        return $pdo;
    }

    /**
     * Throws an exception if $book contains an invalid key.
     *
     * @param $book array
     *
     * @throws \Exception
     */
    private function verifyBook($book)
    {
        if ($invalid = array_diff_key($book, array_flip($this->columnBookNames))) {
            throw new \Exception(sprintf(
                'unsupported book properties: "%s"',
                implode(', ', $invalid)
            ));
        }
    }

    public function listBooks($limit = 10, $cursor = null)
    {
        $pdo = $this->newConnection();
        if ($cursor) {
            $query = 'SELECT * FROM books WHERE id > :cursor ORDER BY id' .
                ' LIMIT :limit';
            $statement = $pdo->prepare($query);
            $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
        } else {
            $query = 'SELECT * FROM books ORDER BY id LIMIT :limit';
            $statement = $pdo->prepare($query);
        }
        $statement->bindValue(':limit', $limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        $last_row = null;
        $new_cursor = null;
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            if (count($rows) == $limit) {
                $new_cursor = $last_row['id'];
                break;
            }
            array_push($rows, $row);
            $last_row = $row;
        }

        return array(
            'books' => $rows,
            'cursor' => $new_cursor,
        );
    }

    public function create($book, $id = null)
    {
        $this->verifyBook($book);
        if ($id) {
            $book['id'] = $id;
        }
        $pdo = $this->newConnection();
        $names = array_keys($book);
        $placeHolders = array_map(function ($key) {
            return ":$key";
        }, $names);
        $sql = sprintf(
            'INSERT INTO books (%s) VALUES (%s)',
            implode(', ', $names),
            implode(', ', $placeHolders)
        );
        $statement = $pdo->prepare($sql);
        $statement->execute($book);

        return $pdo->lastInsertId();
    }

    public function read($id)
    {
        $pdo = $this->newConnection();
        $statement = $pdo->prepare('SELECT * FROM books WHERE id = :id');
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function update($book)
    {
        $this->verifyBook($book);
        $pdo = $this->newConnection();
        $assignments = array_map(
            function ($column) {
                return "$column=:$column";
            },
            $this->columnBookNames
        );
        $assignmentString = implode(',', $assignments);
        $sql = "UPDATE books SET $assignmentString WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $values = array_merge(
            array_fill_keys($this->columnBookNames, null),
            $book
        );
        return $statement->execute($values);
    }

    public function delete($id)
    {
        $pdo = $this->newConnection();
        $statement = $pdo->prepare('DELETE FROM books WHERE id = :id');
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount();
    }
    
    public function register($id, $name, $email, $password)
    {
    	
    	$user_token = $this->generateToken(rand());
    	$hash = $this->hashSSHA($password);
    	$encrypted_password = $hash['password'];
    	$salt = $hash['salt'];
    	
    	$pdo = $this->newConnection();
        $sql = "INSERT INTO Users (id, token, name, email, password, salt) VALUES (:id, :token, :name, :email, :password, :salt)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_STR);
        $statement->bindValue(':token', $user_token, PDO::PARAM_STR);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->bindValue(':password', $encrypted_password, PDO::PARAM_STR);
        $statement->bindValue(':salt', $salt, PDO::PARAM_STR);
        $statement->execute();
        
        $user_uid = $pdo->lastInsertId();
        
        $user_details = $this->getUserProfile($user_uid, $user_uid, $pdo);
	    
	    return $user_details;
    }
    
    public function login($id, $password){
    	
    	$pdo = $this->newConnection();
        $sql = "SELECT * FROM Users WHERE email = :email OR id = :id LIMIT 1";
        $statement = $pdo->prepare($sql);
       	$statement->bindValue(':email', $id, PDO::PARAM_STR);
       	$statement->bindValue(':id', $id, PDO::PARAM_STR);
       	$statement->execute();
       	$user = $statement->fetch(PDO::FETCH_ASSOC);
       	
       	$encrypted_password = $user['password'];
        $salt = $user['salt'];
            
        $hash = $this->checkhashSSHA($salt, $password);
        if($encrypted_password == $hash){
            	//Authentication success
            	
            $token = $this->updateUserToken($user['user_uid']);
			$user['token'] = $token;
            	
           	return $user;
        }

		return false;

    }
    
    public function logout($user_uid, $pdo = null){
    	
    	if($pdo == null){
    		$pdo = $this->newConnection();
    	}
    	
    	$sql = "UPDATE Users SET gcm_id = 'NULL' WHERE user_uid = :user_uid LIMIT 1";
    	$statement = $pdo->prepare($sql);
    	$statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
    	
		return $statement->execute();
    	
    }


	/**
     * Check user is existed or not
     */
    public function isExistingEmail($email, $pdo = null) {
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
		$sql = "SELECT count(*) FROM Users WHERE email = :email LIMIT 1";
		$statement = $pdo->prepare($sql);
		$statement->bindValue(':email', $email, PDO::PARAM_STR);
		$statement->execute();
		
    	$rows = $statement->fetchColumn(); 
		
		if($rows > 0){
			return true;
		}
		
		return false;
    }
    
    public function isExistingID($id, $pdo = null) {
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
    	
    	$sql = "SELECT count(*) FROM Users WHERE id = :id LIMIT 1";
    	$statement = $pdo->prepare($sql);
    	$statement->bindValue(':id', $id, PDO::PARAM_STR);
    	$statement->execute();
    	
    	$rows = $statement->fetchColumn(); 
		
		if($rows > 0){
			return true;
		}
		
		return false;
    }
    
    public function isExistingPageName($name, $pdo = null) {
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
    	//$rows = $pdo->query("SELECT count(*) FROM Pages WHERE title = '$name' LIMIT 1")->fetchColumn(); 
    	$sql = "SELECT count(*) FROM Pages WHERE deleted = FALSE AND title = :name LIMIT 1";
    	$statement = $pdo->prepare($sql);
    	$statement->bindValue(':name', $name, PDO::PARAM_STR);
    	
		$statement->execute();
    	$rows = $statement->fetchColumn();
		
		if($rows > 0){
			return true;
		}
		
		return false;
    }
    
    public function isPageOwnName($page_uid, $name, $pdo = null) {
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
    	$sql = "SELECT count(*) FROM Pages WHERE title = :name AND page_uid = :uid LIMIT 1";
    	$statement = $pdo->prepare($sql);
    	$statement->bindValue(':name', $name, PDO::PARAM_STR);
    	$statement->bindValue(':uid', $page_uid, PDO::PARAM_STR);
    	
		$statement->execute();
    	$rows = $statement->fetchColumn();
		
		if($rows > 0){
			return true;
		}
		
		return false;
    }
    
    public function isPageWithPostUpdateWithin24($page_uid, $pdo = null){
    	
    	if($pdo == null){
    		$pdo = $this->newConnection();
    	}
    	
    	$sql_post = "SELECT count(post_uid) FROM Posts WHERE deleted = FALSE AND (type = 'page' AND user_uid = :page_uid AND (created_at >= NOW() - INTERVAL 1 DAY)) LIMIT 1";
    	$statement_post = $pdo->prepare($sql_post);
    	$statement_post->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
    	$statement_post->execute();
    	$rows_post = $statement_post->fetchColumn();
    	
    	if($rows_post > 0){
			return true;
		}
		
		return false;
    }
    
    public function isPageWithFriendPostCheckinWithin24($page_uid, $user_uid, $pdo = null){
    	
    	if($pdo == null){
    		$pdo = $this->newConnection();
    	}
    	
    	$sql_post = "SELECT count(post_uid) FROM Posts WHERE deleted = FALSE AND checkin_page_uid = :page_uid AND (created_at > NOW() - INTERVAL 1 DAY) AND ((type = 'user') OR (type = 'page' AND user_uid != :page_uid1)) AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) LIMIT 1";
    	$statement_post = $pdo->prepare($sql_post);
    	$statement_post->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
    	$statement_post->bindValue(':page_uid1', $page_uid, PDO::PARAM_STR);
    	$statement_post->execute();
    	$rows_post = $statement_post->fetchColumn();
    	
    	if($rows_post > 0){
			return true;
		}
		
		return false;
    }
    
    public function isPageWithEventUpdateWithin24($page_uid, $pdo = null){
    	
    	if($pdo == null){
    		$pdo = $this->newConnection();
    	}
    	
    	$sql_event = "SELECT count(event_uid) FROM Events WHERE deleted = FALSE AND (organiser_type = 'page' AND organiser_uid = :page_uid AND end_date >= NOW()) LIMIT 1";
    	$statement_event = $pdo->prepare($sql_event);
    	$statement_event->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
    	$statement_event->execute();
    	$rows_event = $statement_event->fetchColumn();
    	
    	if($rows_event > 0){
			return true;
		}
		
		return false;
    }
    
    public function isPageWithFriendEventCheckinWithin24($page_uid, $user_uid, $pdo = null){
    	
    	if($pdo == null){
    		$pdo = $this->newConnection();
    	}
    	
    	$sql_event = "SELECT count(event_uid) FROM Events WHERE deleted = FALSE AND checkin_page_uid = :page_uid AND end_date > NOW() AND ((organiser_type = 'user') OR (organiser_type = 'page' AND organiser_uid != :page_uid1)) AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) LIMIT 1";
    	$statement_event = $pdo->prepare($sql_event);
    	$statement_event->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
    	$statement_event->bindValue(':page_uid1', $page_uid, PDO::PARAM_STR);
    	$statement_event->execute();
    	$rows_event = $statement_event->fetchColumn();
    	
    	if($rows_event > 0){
			return true;
		}
		
		return false;
    }

	public function getNearbyPosts($latitude, $longitude, $user_uid, $cursor = null, $pdo = null){
		
		$max_latitude = $latitude + 0.25;
        $min_latitude = $latitude - 0.25;
        $max_longitude = $longitude + 0.25;
        $min_longitude = $longitude - 0.25;
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
		if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
      
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);

            $cursor = $row['created_at'];
        }

        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );
		
    }
    
    public function searchNearbyPosts($latitude, $longitude, $user_uid, $keyword, $cursor = null, $pdo = null){
		
		$max_latitude = $latitude + 0.25;
        $min_latitude = $latitude - 0.25;
        $max_longitude = $longitude + 0.25;
        $min_longitude = $longitude - 0.25;
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
		if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND (LOWER(description) LIKE :keyword1 OR LOWER(address) LIKE :keyword2) AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND (LOWER(description) LIKE :keyword1 OR LOWER(address) LIKE :keyword2) AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

		$statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':limit', 21, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
     
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            
            $cursor = $row['created_at'];
        }

        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );
		
    }
    
    public function getNearbyRecentPosts($latitude, $longitude, $user_uid, $cursor = null, $pdo = null){
		
		$max_latitude = $latitude + 0.25;
        $min_latitude = $latitude - 0.25;
        $max_longitude = $longitude + 0.25;
        $min_longitude = $longitude - 0.25;
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
		if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND (created_at >= NOW() - INTERVAL 1 DAY) AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND (created_at >= NOW() - INTERVAL 1 DAY) AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        
        $statement->bindValue(':limit', 21, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
    
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            
            $cursor = $row['created_at'];
        }
     
        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );
		
    }
    
    public function getPageRecentCheckinPosts($user_uid, $page_uid, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
		if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND (created_at >= NOW() - INTERVAL 1 DAY) AND ((checkin_page_uid = :page_uid AND type = 'user' AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid))))) OR (user_uid = :page_uid2 AND type = 'page')) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND (created_at >= NOW() - INTERVAL 1 DAY) AND ((checkin_page_uid = :page_uid AND type = 'user' AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid))))) OR (user_uid = :page_uid2 AND type = 'page')) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }
        
        $statement->bindValue(':limit', 21, PDO::PARAM_INT);
        $statement->bindValue(':page_uid', $page_uid);
        $statement->bindValue(':page_uid2', $page_uid);
        $statement->execute();
        $rows = array();
    
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            
            $cursor = $row['created_at'];
        }
     
        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );
		
    }
    
	public function getNearbyEvents($latitude, $longitude, $user_uid, $cursor = null, $pdo = null){
		
		$max_latitude = $latitude + 0.25;
        $min_latitude = $latitude - 0.25;
        $max_longitude = $longitude + 0.25;
        $min_longitude = $longitude - 0.25;
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
		if($cursor != null){
	        $query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND end_date >= NOW() AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND end_date >= NOW() AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
     
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      		$detail = $this->rp->getEventFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
    
        return array(
            'event' => $rows,
            'cursor' => $cursor,
        );
	
    }
    
    public function searchNearbyEvents($latitude, $longitude, $user_uid, $keyword, $cursor = null, $pdo = null){
		
		$max_latitude = $latitude + 0.25;
        $min_latitude = $latitude - 0.25;
        $max_longitude = $longitude + 0.25;
        $min_longitude = $longitude - 0.25;
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
		if($cursor != null){
	        $query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND end_date >= NOW() AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND end_date >= NOW() AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

		$statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':keyword3', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
     
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      		$detail = $this->rp->getEventFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
      
        return array(
            'event' => $rows,
            'cursor' => $cursor,
        );
	
    }

    public function getNearbyPages($latitude, $longitude, $user_uid, $cursor_checkin = null, $cursor_date = null, $pdo = null){

        $max_latitude = $latitude + 0.25;
        $min_latitude = $latitude - 0.25;
        $max_longitude = $longitude + 0.25;
        $min_longitude = $longitude - 0.25;
        
        if($pdo == null){
			$pdo = $this->newConnection();
		}
		
       	if($cursor_date != null){
	        $query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude) AND (longitude BETWEEN $min_longitude AND $max_longitude) AND ((checkin_count = :cursor_checkin AND created_at < :cursor_date) OR checkin_count < :cursor_checkin2) ORDER BY checkin_count DESC, created_at DESC LIMIT :limit";
		    $statement = $pdo->prepare($query);
		    $statement->bindValue(':cursor_checkin', $cursor_checkin, PDO::PARAM_INT);
		    $statement->bindValue(':cursor_checkin2', $cursor_checkin, PDO::PARAM_INT);
		    $statement->bindValue(':cursor_date', $cursor_date, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude) AND (longitude BETWEEN $min_longitude AND $max_longitude) ORDER BY checkin_count DESC, created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }
      
       
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $detail = $this->rp->getPageFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor_checkin = $row['checkin_count'];
            $cursor_date = $row['created_at'];
        }
       
        return array(
            'page' => $rows,
            'cursor_checkin' => $cursor_checkin,
            'cursor_date' => $cursor_date,
        );
		
    }
    
    public function searchNearbyPages($latitude, $longitude, $user_uid, $keyword, $category, $cursor_checkin = null, $cursor_date = null, $pdo = null){

        $max_latitude = $latitude + 0.25;
        $min_latitude = $latitude - 0.25;
        $max_longitude = $longitude + 0.25;
        $min_longitude = $longitude - 0.25;
        
        if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor_date != null){
        	$query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) AND category LIKE :category AND ((checkin_count = :cursor_checkin AND created_at < :cursor_date) OR checkin_count < :cursor_checkin2) ORDER BY checkin_count DESC, created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor_checkin', $cursor_checkin, PDO::PARAM_INT);
	        $statement->bindValue(':cursor_checkin2', $cursor_checkin, PDO::PARAM_INT);
		    $statement->bindValue(':cursor_date', $cursor_date, PDO::PARAM_STR);
        }
        else{
        	$query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) AND category LIKE :category ORDER BY checkin_count DESC, created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
        }

		$statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':keyword3', "%$keyword%", PDO::PARAM_STR); 
		$statement->bindValue(':category', "%$category%", PDO::PARAM_STR);        
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $detail = $this->rp->getPageFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor_checkin = $row['checkin_count'];
            $cursor_date = $row['created_at'];
        }
      
        return array(
            'page' => $rows,
            'cursor_checkin' => $cursor_checkin,
            'cursor_date' => $cursor_date,
        );
		
    }
    
   public function getPagesAtLocation($latitude, $longitude, $user_uid, $cursor_checkin = null, $cursor_date = null, $pdo = null){

        $max_latitude = $latitude + 0.001;
        $min_latitude = $latitude - 0.001;
        $max_longitude = $longitude + 0.001;
        $min_longitude = $longitude - 0.001;
        
        if($pdo == null){
			$pdo = $this->newConnection();
		}
		
       	if($cursor_date != null){
	        $query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude) AND (longitude BETWEEN $min_longitude AND $max_longitude) AND ((checkin_count = :cursor_checkin AND created_at < :cursor_date) OR checkin_count < :cursor_checkin2) ORDER BY checkin_count DESC, created_at DESC LIMIT :limit";
		    $statement = $pdo->prepare($query);
		    $statement->bindValue(':cursor_checkin', $cursor_checkin, PDO::PARAM_INT);
		    $statement->bindValue(':cursor_checkin2', $cursor_checkin, PDO::PARAM_INT);
		    $statement->bindValue(':cursor_date', $cursor_date, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude) AND (longitude BETWEEN $min_longitude AND $max_longitude) ORDER BY checkin_count DESC, created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }
      
       
        $statement->bindValue(':limit', 21, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $detail = $this->rp->getPageFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor_checkin = $row['checkin_count'];
            $cursor_date = $row['created_at'];
        }
       
        return array(
            'page' => $rows,
            'cursor_checkin' => $cursor_checkin,
            'cursor_date' => $cursor_date,
        );
		
    }
    
     public function searchPagesAtLocation($latitude, $longitude, $user_uid, $keyword, $cursor_checkin = null, $cursor_date = null, $pdo = null){

        $max_latitude = $latitude + 0.001;
        $min_latitude = $latitude - 0.001;
        $max_longitude = $longitude + 0.001;
        $min_longitude = $longitude - 0.001;
        
        if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor_date != null){
        	$query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) AND ((checkin_count = :cursor_checkin AND created_at < :cursor_date) OR checkin_count < :cursor_checkin2) ORDER BY checkin_count DESC, created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor_checkin', $cursor_checkin, PDO::PARAM_INT);
	        $statement->bindValue(':cursor_checkin2', $cursor_checkin, PDO::PARAM_INT);
		    $statement->bindValue(':cursor_date', $cursor_date, PDO::PARAM_STR);
        }
        else{
        	$query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE deleted = FALSE AND (latitude BETWEEN $min_latitude AND $max_latitude AND longitude BETWEEN $min_longitude AND $max_longitude) AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) ORDER BY checkin_count DESC, created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
        }
        
        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':keyword3', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':limit', 21, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $detail = $this->rp->getPageFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor_checkin = $row['checkin_count'];
            $cursor_date = $row['created_at'];
        }
      
        return array(
            'page' => $rows,
            'cursor_checkin' => $cursor_checkin,
            'cursor_date' => $cursor_date,
        );
		
    }


 	public function getNearbyUsers($latitude, $longitude, $user_uid, $cursor = null, $pdo = null){
 		
 		$max_latitude = $latitude + 0.25;
        $min_latitude = $latitude - 0.25;
        $max_longitude = $longitude + 0.25;
        $min_longitude = $longitude - 0.25;
        
        if($pdo == null){
			$pdo = $this->newConnection();
		}
       
        $query = "SELECT * FROM Users WHERE (last_latitude BETWEEN $min_latitude AND $max_latitude) AND (last_longitude BETWEEN $min_longitude AND $max_longitude) ORDER BY user_uid ASC LIMIT :limit OFFSET :cursor";
        $statement = $pdo->prepare($query);
        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
       
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getUserFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row[' '];
        }
      

        return array(
            'user' => $rows,
            'cursor' => $cursor,
        );
		
    }
    
    public function getUserPages($user_uid, $search_user_uid, $cursor_checkin = null, $cursor_date = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor_date != null){
	        $query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE deleted = FALSE AND user_uid = :user_uid AND ((checkin_count = :cursor_checkin AND created_at < :cursor_date) OR checkin_count < :cursor_checkin2) ORDER BY checkin_count DESC, created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor_checkin', $cursor_checkin, PDO::PARAM_INT);
	        $statement->bindValue(':cursor_checkin2', $cursor_checkin, PDO::PARAM_INT);
		    $statement->bindValue(':cursor_date', $cursor_date, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE deleted = FALSE AND user_uid = :user_uid ORDER BY checkin_count DESC, created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':user_uid', $search_user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getPageFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor_checkin = $row['checkin_count'];
            $cursor_date = $row['created_at'];
        }
      

        return array(
            'page' => $rows,
            'cursor_checkin' => $cursor_checkin,
            'cursor_date' => $cursor_date,
        );
    	
    }
    
    public function searchUserPages($user_uid, $search_user_uid, $keyword, $category, $cursor_checkin = null, $cursor_date = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}
       
        if($cursor_date != null){
        	$query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE deleted = FALSE AND user_uid = :user_uid AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) AND category LIKE :category AND ((checkin_count = :cursor_checkin AND created_at < :cursor_date) OR checkin_count < :cursor_checkin2) ORDER BY checkin_count DESC, created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor_checkin', $cursor_checkin, PDO::PARAM_INT);
	        $statement->bindValue(':cursor_checkin2', $cursor_checkin, PDO::PARAM_INT);
		    $statement->bindValue(':cursor_date', $cursor_date, PDO::PARAM_STR);
	    }
	    else{
        	$query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE deleted = FALSE AND user_uid = :user_uid AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) AND category LIKE :category ORDER BY checkin_count DESC, created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

		$statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':keyword3', "%$keyword%", PDO::PARAM_STR); 
       	$statement->bindValue(':category', "%$category%", PDO::PARAM_STR);
        $statement->bindValue(':user_uid', $search_user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getPageFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor_checkin = $row['checkin_count'];
            $cursor_date = $row['created_at'];
        }
      

        return array(
            'page' => $rows,
            'cursor_checkin' => $cursor_checkin,
            'cursor_date' => $cursor_date,
        );
    	
    }

	public function getUserPosts($user_uid, $search_user_uid, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
       
        if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND user_uid = :user_uid AND type = 'user' AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND user_uid = :user_uid AND type = 'user' ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':user_uid', $search_user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
       

        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );

    }
    
    public function searchUserPosts($user_uid, $search_user_uid, $keyword, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND user_uid = :user_uid AND type = 'user' AND (LOWER(description) LIKE :keyword1 OR LOWER(address) LIKE :keyword2) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND user_uid = :user_uid AND type = 'user' AND (LOWER(description) LIKE :keyword1 OR LOWER(address) LIKE :keyword2) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':user_uid', $search_user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
       

        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );

    }
    
    public function getUserRecentPosts($user_uid, $search_user_uid, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
       
        if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND user_uid = :user_uid AND type = 'user' AND (created_at >= NOW() - INTERVAL 1 DAY) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND user_uid = :user_uid AND type = 'user' AND (created_at >= NOW() - INTERVAL 1 DAY) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':user_uid', $search_user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
       

        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );

    }
    
    public function getUserFlags($user_uid, $search_user_uid, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor != null){
	        $query = "SELECT * FROM Flags WHERE deleted = FALSE AND user_uid = :user_uid AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT * FROM Flags WHERE deleted = FALSE AND user_uid = :user_uid ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        
        $statement->bindValue(':user_uid', $search_user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getFlagFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
       

        return array(
            'flag' => $rows,
            'cursor' => $cursor,
        );

    }
    
    public function searchUserFlags($user_uid, $search_user_uid, $keyword, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor != null){
	        $query = "SELECT * FROM Flags WHERE deleted = FALSE AND user_uid = :user_uid AND (LOWER(description) LIKE :keyword1) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT * FROM Flags WHERE deleted = FALSE AND user_uid = :user_uid AND (LOWER(description) LIKE :keyword1) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':user_uid', $search_user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getFlagFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
     

        return array(
            'flag' => $rows,
            'cursor' => $cursor,
        );

    }
    
    
    public function getUserEvents($user_uid, $search_user_uid, $cursor = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor != null){
	        $query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND organiser_uid = :user_uid AND organiser_type = 'user' AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND organiser_uid = :user_uid AND organiser_type = 'user' ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':user_uid', $search_user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getEventFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
       

        return array(
            'event' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    public function searchUserEvents($user_uid, $search_user_uid, $keyword, $cursor = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}
       
        if($cursor != null){
	        $query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND organiser_uid = :user_uid AND organiser_type = 'user' AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND organiser_uid = :user_uid AND organiser_type = 'user' AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':keyword3', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':user_uid', $search_user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getEventFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
     

        return array(
            'event' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    public function getUserRecentEvents($user_uid, $search_user_uid, $cursor = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor != null){
	        $query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND organiser_type = 'user' AND organiser_uid = :user_uid AND end_date > NOW() AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND organiser_type = 'user' AND organiser_uid = :user_uid AND end_date > NOW() ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':user_uid', $search_user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getEventFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
       

        return array(
            'event' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
     public function getUserJoinedEvents($user_uid, $cursor = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor != null){
	        $query = "SELECT Events.*, EventAttenders.* FROM Events, EventAttenders WHERE Events.deleted = FALSE AND Events.event_uid = EventAttenders.event_uid AND EventAttenders.user_uid = :user_uid AND Events.end_date >= NOW() AND EventAttenders.created_at > :cursor ORDER BY EventAttenders.created_at ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
			$query = "SELECT Events.*, EventAttenders.* FROM Events, EventAttenders WHERE Events.deleted = FALSE AND Events.event_uid = EventAttenders.event_uid AND EventAttenders.user_uid = :user_uid AND Events.end_date >= NOW() ORDER BY EventAttenders.created_at ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getEventFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
       

        return array(
            'event' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    
    public function getUserFollowingPages($user_uid, $cursor_checkin = null, $cursor_date = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor_date != null){
	        $query = "SELECT Pages.* FROM Pages, Requests WHERE Pages.deleted = FALSE AND Requests.type = 'page' AND Pages.page_uid = Requests.to_user_uid AND Requests.from_user_uid = :user_uid AND ((Pages.checkin_count = :cursor_checkin AND Pages.created_at < :cursor_date) OR Pages.checkin_count < :cursor_checkin2) ORDER BY Pages.checkin_count DESC, Pages.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor_checkin', $cursor_checkin, PDO::PARAM_INT);
	        $statement->bindValue(':cursor_checkin2', $cursor_checkin, PDO::PARAM_INT);
		    $statement->bindValue(':cursor_date', $cursor_date, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Pages.* FROM Pages, Requests WHERE Pages.deleted = FALSE AND Requests.type = 'page' AND Pages.page_uid = Requests.to_user_uid AND Requests.from_user_uid = :user_uid ORDER BY Pages.checkin_count DESC, Pages.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getPageFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor_checkin = $row['checkin_count'];
            $cursor_date = $row['created_at'];
        }
      

        return array(
            'page' => $rows,
            'cursor_checkin' => $cursor_checkin,
            'cursor_date' => $cursor_date,
        );
    	
    }
    
    public function searchUserFollowingPages($user_uid, $keyword, $category, $cursor_checkin = null, $cursor_date = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor_date != null){
	        $query = "SELECT Pages.* FROM Pages, Requests WHERE Pages.deleted = FALSE AND Requests.type = 'page' AND Pages.page_uid = Requests.to_user_uid AND Requests.from_user_uid = :user_uid AND (LOWER(Pages.title) LIKE :keyword1 OR LOWER(Pages.description) LIKE :keyword2 OR LOWER(Pages.address) LIKE :keyword3) AND Pages.category LIKE :category AND ((Pages.checkin_count = :cursor_checkin AND Pages.created_at < :cursor_date) OR Pages.checkin_count < :cursor_checkin2) ORDER BY Pages.checkin_count DESC, Pages.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor_checkin', $cursor_checkin, PDO::PARAM_INT);
	        $statement->bindValue(':cursor_checkin2', $cursor_checkin, PDO::PARAM_INT);
		    $statement->bindValue(':cursor_date', $cursor_date, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Pages.* FROM Pages, Requests WHERE Pages.deleted = FALSE AND Requests.type = 'page' AND Pages.page_uid = Requests.to_user_uid AND Requests.from_user_uid = :user_uid AND (LOWER(Pages.title) LIKE :keyword1 OR LOWER(Pages.description) LIKE :keyword2 OR LOWER(Pages.address) LIKE :keyword3) AND Pages.category LIKE :category ORDER BY Pages.checkin_count DESC, Pages.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

		$statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':keyword3', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':category', "%$category%", PDO::PARAM_STR);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getPageFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor_checkin = $row['checkin_count'];
            $cursor_date = $row['created_at'];
        }
      

        return array(
            'page' => $rows,
            'cursor_checkin' => $cursor_checkin,
            'cursor_date' => $cursor_date,
        );
    	
    }
    
    public function getUserFollowingPagesForSubscription($user_uid, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}
        

	    $query = "SELECT Pages.page_uid FROM Pages, Requests WHERE Requests.type = 'page' AND Pages.page_uid = Requests.to_user_uid AND Requests.from_user_uid = :user_uid";
	    $statement = $pdo->prepare($query);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_INT);
        $statement->execute();

        return array(
            'page' => $statement->fetchAll(PDO::FETCH_COLUMN, 0),
        );
    	
    }
    
    public function getUserFollowingAndOwnedPages($user_uid, $cursor_checkin = null, $cursor_date = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor_date != null){
	        $query = "SELECT Pages.* FROM Pages, Requests WHERE Pages.deleted = FALSE AND ((Requests.type = 'page' AND Pages.page_uid = Requests.to_user_uid AND Requests.from_user_uid = :user_uid) OR (Pages.user_uid = :user_uid2)) AND ((Pages.checkin_count = :cursor_checkin AND Pages.created_at < :cursor_date) OR Pages.checkin_count < :cursor_checkin2) ORDER BY Pages.checkin_count DESC, Pages.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor_checkin', $cursor_checkin, PDO::PARAM_INT);
	        $statement->bindValue(':cursor_checkin2', $cursor_checkin, PDO::PARAM_INT);
		    $statement->bindValue(':cursor_date', $cursor_date, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Pages.* FROM Pages, Requests WHERE Pages.deleted = FALSE AND ((Requests.type = 'page' AND Pages.page_uid = Requests.to_user_uid AND Requests.from_user_uid = :user_uid) OR (Pages.user_uid = :user_uid2)) ORDER BY Pages.checkin_count DESC, Pages.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_INT);
        $statement->bindValue(':user_uid2', $user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', 20 + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getPageFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor_checkin = $row['checkin_count'];
            $cursor_date = $row['created_at'];
        }
      

        return array(
            'page' => $rows,
            'cursor_checkin' => $cursor_checkin,
            'cursor_date' => $cursor_date,
        );
    	
    }
    
    public function getPagePosts($page_uid, $user_uid, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND user_uid = :user_uid AND type = 'page' AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND user_uid = :user_uid AND type = 'page' ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':user_uid', $page_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
      

        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );

    }
    
    public function searchPagePosts($page_uid, $user_uid, $keyword, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND user_uid = :user_uid AND type = 'page' AND (LOWER(description) LIKE :keyword1 OR LOWER(address) LIKE :keyword2) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND user_uid = :user_uid AND type = 'page' AND (LOWER(description) LIKE :keyword1 OR LOWER(address) LIKE :keyword2) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':user_uid', $page_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
      

        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );

    }
    
    public function getPageRecentPosts($page_uid, $user_uid, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND type = 'page' AND user_uid = :user_uid AND (created_at >= NOW() - INTERVAL 1 DAY) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND type = 'page' AND user_uid = :user_uid AND (created_at >= NOW() - INTERVAL 1 DAY) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':user_uid', $page_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
      

        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );

    }
    
    public function searchPageRecentPosts($page_uid, $user_uid, $keyword, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND type = 'page' AND user_uid = :user_uid AND (created_at >= NOW() - INTERVAL 1 DAY) AND (LOWER(description) LIKE :keyword1 OR LOWER(address) LIKE :keyword2) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND type = 'page' AND user_uid = :user_uid AND (created_at >= NOW() - INTERVAL 1 DAY) AND (LOWER(description) LIKE :keyword1 OR LOWER(address) LIKE :keyword2) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':user_uid', $page_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
      

        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );

    }
    
     public function getPageEvents($user_uid, $page_uid, $cursor = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor != null){
	        $query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND (organiser_uid = :page_uid AND organiser_type = 'page') OR (checkin_page_uid = $page_uid AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid))))) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND (organiser_uid = :page_uid AND organiser_type = 'page') OR (checkin_page_uid = $page_uid AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid))))) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getEventFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
    

        return array(
            'event' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    public function searchPageEvents($user_uid, $page_uid, $keyword, $cursor = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND ((organiser_uid = :page_uid AND organiser_type = 'page') OR (checkin_page_uid = $page_uid AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))))) AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND ((organiser_uid = :page_uid AND organiser_type = 'page') OR (checkin_page_uid = $page_uid AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))))) AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':keyword3', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getEventFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
     
        return array(
            'event' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    public function getPageRecentEvents($user_uid, $page_uid, $cursor = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor != null){
	        $query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND end_date > NOW() AND ((organiser_uid = :page_uid AND organiser_type = 'page') OR (checkin_page_uid = $page_uid AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))))) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND end_date > NOW() AND ((organiser_uid = :page_uid AND organiser_type = 'page') OR (checkin_page_uid = $page_uid AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))))) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getEventFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
    

        return array(
            'event' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    public function searchPageRecentEvents($user_uid, $page_uid, $keyword, $cursor = null, $pdo = null){
    
        if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND end_date > NOW() AND ((organiser_uid = :page_uid AND organiser_type = 'page') OR (checkin_page_uid = $page_uid AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))))) AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE deleted = FALSE AND end_date > NOW() AND ((organiser_uid = :page_uid AND organiser_type = 'page') OR (checkin_page_uid = $page_uid AND (visibility = 'public' OR organiser_uid = $user_uid OR organiser_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))))) AND (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':keyword3', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getEventFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
     
        return array(
            'event' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    public function getPageCheckInPosts($page_uid, $user_uid, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND checkin_page_uid = :checkin_page_uid AND user_uid != :page_uid AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND checkin_page_uid = :checkin_page_uid AND user_uid != :page_uid AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        
        $statement->bindValue(':checkin_page_uid', $page_uid, PDO::PARAM_INT);
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
      

        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );

    }
    
    public function searchPageCheckInPosts($page_uid, $user_uid, $keyword, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND checkin_page_uid = :checkin_page_uid AND user_uid != :page_uid AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) AND (LOWER(description) LIKE :keyword1 OR LOWER(address) LIKE :keyword2) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE deleted = FALSE AND checkin_page_uid = :checkin_page_uid AND user_uid != :page_uid AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) AND (LOWER(description) LIKE :keyword1 OR LOWER(address) LIKE :keyword2) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':checkin_page_uid', $page_uid, PDO::PARAM_INT);
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
      

        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );

    }

    public function getUserFriends($user_uid, $status, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor != null){
	        $query = "SELECT Requests.*, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = :status AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)) AND Requests.request_uid > :cursor ORDER BY Requests.request_uid ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Requests.*, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = :status AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)) ORDER BY Requests.request_uid ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

      
        $statement->bindValue(':status', $status, PDO::PARAM_STR);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
     
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          
            //$detail = $this->rp->getRequestFromQuery($row, $this, $user_uid);
            array_push($rows, $row);
            $cursor = $row['request_uid'];
        }
      

        return array(
            'friend' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
     public function searchUserFriends($user_uid, $status, $keyword, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        if($cursor != null){
	        $query = "SELECT Requests.*, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = :status AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)) AND (LOWER(Users.name) LIKE :keyword1 OR LOWER(Users.id) LIKE :keyword2) AND Requests.request_uid > :cursor ORDER BY Requests.request_uid ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Requests.*, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = :status AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)) AND (LOWER(Users.name) LIKE :keyword1 OR LOWER(Users.id) LIKE :keyword2) ORDER BY Requests.request_uid ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':status', $status, PDO::PARAM_STR);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          
            //$detail = $this->rp->getRequestFromQuery($row, $this, $user_uid);
            array_push($rows, $row);
            $cursor = $row['request_uid'];
        }
       

        return array(
            'friend' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    public function getFriendStatus($user_uid, $other_user_uid, $pdo = null){
    	
    	if($pdo == null){
	    	$pdo = $this->newConnection();
		}

        $sql = "SELECT * FROM Requests WHERE type = 'user' AND ((from_user_uid = '$user_uid' AND to_user_uid = '$other_user_uid') OR (from_user_uid = '$other_user_uid' AND to_user_uid = '$user_uid')) LIMIT 1";
        $result = $pdo->query($sql);
        
        while ($row = $result->fetch()) {
        	if($row['to_user_uid'] == $user_uid && $row['status'] == 'pending'){
        		return 'accepting';
        	}
			return $row['status'];
		}
		return 'normal';

    }
    
     public function getFollowPageStatus($user_uid, $page_uid, $pdo = null){
     	
     	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
    	$num_row = $pdo->query("SELECT count(*) FROM Requests WHERE type = 'page' AND from_user_uid = '$user_uid' AND to_user_uid = '$page_uid' LIMIT 1")->fetchColumn(); 
		
		if($num_row > 0){
			return true;
		}
		
		return false;
    	
    }
    
    public function getUserNotifications($user_uid, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT * FROM Notifications WHERE user_uid = :user_uid AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT * FROM Notifications WHERE user_uid = :user_uid ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

      
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', 21, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getNotificationFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
      

        return array(
            'notification' => $rows,
            'cursor' => $cursor,
        );
    	
    }


	public function storeNotification($user_uid, $receiving_user_uid, $type, $content_type, $pdo = null, $post_uid = "NULL", $tag = "NULL"){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "INSERT INTO Notifications (type, content_user_uid, user_uid, content_type, post_uid, tag) VALUES (:type, :content_user_uid, :user_uid, :content_type, :post_uid, :tag)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':type', $type, PDO::PARAM_STR);
        $statement->bindValue(':content_user_uid', $user_uid, PDO::PARAM_STR);
        $statement->bindValue(':content_type', $content_type, PDO::PARAM_STR);
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_STR);
        $statement->bindValue(':tag', $tag, PDO::PARAM_STR);
        $statement->bindValue(':user_uid', $receiving_user_uid, PDO::PARAM_STR);
        $statement->execute();
        
        $notification_uid = $pdo->lastInsertId();
        $notification_details = $this->getNotificationDetails($notification_uid, $pdo);	
        
        $this->sendNotification($notification_details, $user_uid, $pdo);
	    
	    return true;

    }
    
    public function getNotificationDetails($notification_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "SELECT * FROM Notifications WHERE notification_uid = :notification_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':notification_uid', $notification_uid, PDO::PARAM_STR);
        $statement->execute();
    	
    	$notification = $statement->fetch(PDO::FETCH_ASSOC);
        
     	return $notification;
 	}

        

	public function sendNotification($notification, $user_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        $firebase = new Firebase();
       
        $message['notification'] = $this->rp->getNotificationFromQuery($notification, $this, $user_uid, $pdo);
        
        $receiving_user = $this->getUserProfile($notification['user_uid'], $user_uid, $pdo);
        
        $gcm_id = $receiving_user['gcm_id'];
        
        $result = $firebase->send($gcm_id, $message);
        
        return true;
    }
    
    public function sendNotificationToTopic($topic, $message, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
        
        $firebase = new Firebase();
        
        $result = $firebase->sendToTopic($topic, $message);
        
        return true;
    }
    
    public function sendFriendRequest($from_user_uid, $to_user_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "INSERT INTO Requests (type, from_user_uid, to_user_uid, status) VALUES (:type, :from_user_uid, :to_user_uid, :status)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':type', 'user', PDO::PARAM_STR);
        $statement->bindValue(':from_user_uid', $from_user_uid, PDO::PARAM_STR);
        $statement->bindValue(':to_user_uid', $to_user_uid, PDO::PARAM_STR);
        $statement->bindValue(':status', 'pending', PDO::PARAM_STR);
        
		if($statement->execute()){
        	$this->storeNotification($from_user_uid, $to_user_uid, 'request received', 'user', $pdo);
        	return true;
        }
        else{
        	return false;
        }

    }


    public function acceptFriendRequest($from_user_uid, $to_user_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Requests SET status = :status WHERE type = 'user' AND from_user_uid = :from_user_uid AND to_user_uid = :to_user_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':from_user_uid', $from_user_uid, PDO::PARAM_STR);
        $statement->bindValue(':to_user_uid', $to_user_uid, PDO::PARAM_STR);
        $statement->bindValue(':status', 'accepted', PDO::PARAM_STR);
        
		if($statement->execute()){
			 	$this->incrementUserFriendCount($from_user_uid, $pdo);
		       	$this->incrementUserFriendCount($to_user_uid, $pdo);
		        $this->storeNotification($to_user_uid, $from_user_uid, 'request accepted', 'user', $pdo);
		        
		        return true;
		}
		return false;
    }

    public function removeFriendRequest($from_user_uid, $to_user_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "DELETE FROM Requests WHERE type = 'user' AND ((from_user_uid = $from_user_uid AND to_user_uid = $to_user_uid) OR (from_user_uid = $to_user_uid AND to_user_uid = $from_user_uid)) LIMIT 1";
        $statement = $pdo->prepare($sql);
        
		if($statement->execute()){
			 return true;
		}
		
		return false;
    
    }

	public function followPage($user_uid, $page_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "INSERT INTO Requests (type, from_user_uid, to_user_uid, status) VALUES (:type, :from_user_uid, :to_user_uid, :status)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':type', 'page', PDO::PARAM_STR);
        $statement->bindValue(':from_user_uid', $user_uid, PDO::PARAM_STR);
        $statement->bindValue(':to_user_uid', $page_uid, PDO::PARAM_STR);
        $statement->bindValue(':status', 'accepted', PDO::PARAM_STR);
        
        if($statement->execute()){

			$page_details = $this->getPageDetails($page_uid, $user_uid);
		    
		    $this->incrementPageFollowerCount($page_uid, $pdo);
		  	$this->storeNotification($user_uid, $page_details['user_uid'], 'new follower', 'user', $pdo, $page_uid, 'page');
		  	
        	return true;
        }
        else{
        	return false;
        }
	  	
    }


    public function unfollowPage($user_uid, $page_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "DELETE FROM Requests WHERE type = 'page' AND from_user_uid = :from_user_uid AND to_user_uid = :to_user_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':from_user_uid', $user_uid, PDO::PARAM_STR);
        $statement->bindValue(':to_user_uid', $page_uid, PDO::PARAM_STR);
        
		if($statement->execute()){
			
			 $this->decrementPageFollowerCount($page_uid, $pdo);
			
			 return true;
		}
		
		return false;
   
    }
    
     public function updatePageFame($user_uid, $page_uid, $fame_status){
    	
    	$pdo = $this->newConnection();
    	
    	if($fame_status == "up"){
    		$sql = "UPDATE Pages SET fame_count = fame_count + 1 WHERE page_uid = :page_uid LIMIT 1; UPDATE Users SET last_fame_update = NOW() WHERE user_uid = :user_uid LIMIT 1";
       	 	$statement = $pdo->prepare($sql);
    	}
    	else{
    		$sql = "UPDATE Pages SET fame_count = fame_count - 1 WHERE page_uid = :page_uid LIMIT 1; UPDATE Users SET last_fame_update = NOW() WHERE user_uid = :user_uid LIMIT 1";
        	$statement = $pdo->prepare($sql);
    	}
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        
		if($statement->execute()){
			
			 return true;
		}
		
		return false;
    }
    
    public function allowUserUpdatePageFame($user_uid){
    	
    	$pdo = $this->newConnection();
        $sql = "SELECT DATEDIFF(NOW(), last_fame_update) AS duration FROM Users WHERE user_uid = :user_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_INT);
        $statement->execute();
    	
    	$duration = $statement->fetch(PDO::FETCH_ASSOC);
        
     	return $duration['duration'] >= 1;
    	
    }
    
    public function getPageFollowers($page_uid, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT Requests.*, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM Requests, Users WHERE Requests.type = :type AND Requests.to_user_uid = :to_user_uid AND Requests.from_user_uid = Users.user_uid AND Requests.request_uid > :cursor ORDER BY Requests.request_uid ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Requests.*, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM Requests, Users WHERE Requests.type = :type AND Requests.to_user_uid = :to_user_uid AND Requests.from_user_uid = Users.user_uid ORDER BY Requests.request_uid ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        
        $statement->bindValue(':type', 'page', PDO::PARAM_STR);
        $statement->bindValue(':to_user_uid', $page_uid, PDO::PARAM_STR);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          
            //$detail = $this->rp->getRequestFromQuery($row, $this, $user_uid);
            array_push($rows, $row);
            $cursor = $row['request_uid'];
        }
      

        return array(
            'friend' => $rows,
            'cursor' => $cursor,
        );
	  	
    }
    
    public function searchPageFollowers($page_uid, $keyword, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
		if($cursor != null){
	        $query = "SELECT Requests.*, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM Requests, Users WHERE Requests.type = :type AND Requests.to_user_uid = :to_user_uid AND Requests.from_user_uid = Users.user_uid AND (LOWER(Users.name) LIKE :keyword1 OR LOWER(Users.id) LIKE :keyword2) AND Requests.request_uid > :cursor ORDER BY Requests.request_uid ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Requests.*, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM Requests, Users WHERE Requests.type = :type AND Requests.to_user_uid = :to_user_uid AND Requests.from_user_uid = Users.user_uid AND (LOWER(Users.name) LIKE :keyword1 OR LOWER(Users.id) LIKE :keyword2) ORDER BY Requests.request_uid ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        
        $statement->bindValue(':type', 'page', PDO::PARAM_STR);
		$statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':to_user_uid', $page_uid, PDO::PARAM_STR);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          
            //$detail = $this->rp->getRequestFromQuery($row, $this, $user_uid);
            array_push($rows, $row);
            $cursor = $row['request_uid'];
        }
      

        return array(
            'friend' => $rows,
            'cursor' => $cursor,
        );
	  	
    }

	public function getUserProfile($search_user_uid, $user_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}

        $sql = "SELECT * FROM Users WHERE user_uid = :user_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $search_user_uid, PDO::PARAM_STR);
        $statement->execute();
    	
    	$user_details = $statement->fetch(PDO::FETCH_ASSOC);
        
     	return $user_details;
       
    }
    
     public function updatePageProfileImage($page_uid, $profile_image, $user_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
 
        $query = "UPDATE Pages SET profile_image_small = :profile_image WHERE page_uid = :page_uid LIMIT 1";
        $statement = $pdo->prepare($query);
        $statement->bindValue(':profile_image', $profile_image, PDO::PARAM_STR);
      
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
        $statement->execute();
    	
    	$page_details = $this->getPageDetails($page_uid, $user_uid, $pdo);
        
     	return $page_details;
    }


    public function updateUserProfileImage($user_uid, $profile_image, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
 
        $query = "UPDATE Users SET profile_image_small = :profile_image WHERE user_uid = :user_uid LIMIT 1";
        $statement = $pdo->prepare($query);
        $statement->bindValue(':profile_image', $profile_image, PDO::PARAM_STR);
      
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        $statement->execute();
    	
    	$user_details = $this->getUserProfile($user_uid, $user_uid, $pdo);
        
     	return $user_details;
    }
    
     public function updateUserProfile($user_uid, $name, $email, $profile_image = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
    	if ($profile_image != null) {
            $query = "UPDATE Users SET name = :name, email = :email, profile_image = :profile_image, updated_at = NOW() WHERE user_uid = :user_uid LIMIT 1";
            $statement = $pdo->prepare($query);
            $statement->bindValue(':profile_image', $profile_image, PDO::PARAM_STR);
        } else {
           	$query = "UPDATE Users SET name = :name, email = :email, updated_at = NOW() WHERE user_uid = :user_uid LIMIT 1";
            $statement = $pdo->prepare($query);
        }
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();
    	
    	$user_details = $this->getUserProfile($user_uid, $user_uid, $pdo);
        
     	return $user_details;
    }

	public function changeUserPassword($user_uid, $current_password, $new_password, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
		$user = $this->getUserProfile($user_uid, $user_uid, $pdo);
		
		$encrypted_password = $user['password'];
        $salt = $user['salt'];
            
        $hash = $this->checkhashSSHA($salt, $current_password);
		
		 if($encrypted_password == $hash){
		 	
		 	$new_hash = $this->hashSSHA($new_password);
	    	$new_encrypted_password = $new_hash['password'];
	    	$new_salt = $new_hash['salt'];
		 	
	        $sql = "UPDATE Users SET password = :password, salt = :salt, updated_at = NOW() WHERE user_uid = :user_uid LIMIT 1";
	        $statement = $pdo->prepare($sql);
	        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
	        $statement->bindValue(':password', $new_encrypted_password, PDO::PARAM_STR);
	        $statement->bindValue(':salt', $new_salt, PDO::PARAM_STR);
	        $statement->execute();
            
          	return true;
        }
        else{
        	
            return false;
        }
		
    }
    
    public function changeUserPasswordWithoutChecking($user_id, $new_password){
		
		 	$new_hash = $this->hashSSHA($new_password);
	    	$new_encrypted_password = $new_hash['password'];
	    	$new_salt = $new_hash['salt'];
		 	
		 	$pdo = $this->newConnection();
	        $sql = "UPDATE Users SET password = :password, salt = :salt, updated_at = NOW() WHERE id = :user_id LIMIT 1";
	        $statement = $pdo->prepare($sql);
	        $statement->bindValue(':user_id', $user_id, PDO::PARAM_STR);
	        $statement->bindValue(':password', $new_encrypted_password, PDO::PARAM_STR);
	        $statement->bindValue(':salt', $new_salt, PDO::PARAM_STR);
      		$statement->execute();
      		
      		return false;
    }

	public function updateUserGCM($user_uid, $firebase_id, $pdo = null){
		
			if($pdo == null){
				$pdo = $this->newConnection();
			}
			
	        $sql = "UPDATE Users SET gcm_id = :gcm_id WHERE user_uid = :user_uid LIMIT 1";
	        $statement = $pdo->prepare($sql);
	        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
	        $statement->bindValue(':gcm_id', $firebase_id, PDO::PARAM_STR);
 
        	return $statement->execute();
        
    }
    
    public function updateUserLocation($user_uid, $lat, $lng, $pdo = null){
    	
    		if($pdo == null){
				$pdo = $this->newConnection();
			}
			
	        $sql = "UPDATE Users SET last_longitude = :lng, last_latitude = :lat, last_location_datetime = NOW() WHERE user_uid = :user_uid LIMIT 1";
	        $statement = $pdo->prepare($sql);
	        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
	        $statement->bindValue(':lng', $lng, PDO::PARAM_STR);
	        $statement->bindValue(':lat', $lat, PDO::PARAM_STR);
 
        	return $statement->execute();

    }

    public function updateUserToken($user_uid, $pdo = null){
    	
    		$new_token = $this->generateToken(rand());
    	
    		if($pdo == null){
				$pdo = $this->newConnection();
			}
			
	        $sql = "UPDATE Users SET token = :token WHERE user_uid = :user_uid LIMIT 1";
	        $statement = $pdo->prepare($sql);
	        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
	        $statement->bindValue(':token', $new_token, PDO::PARAM_STR);
	        $statement->execute();
 
        	return $new_token;

    }


    public function getPageDetails($page_uid, $user_uid, $pdo = null){
    	
    	if($pdo == null){
	    	$pdo = $this->newConnection();
		}

        $sql = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE page_uid = :page_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
     
        if($statement->execute()){
    		$page_details = $statement->fetch(PDO::FETCH_ASSOC);
    		
        	return $page_details;
        }
		else{
			return false;
		}
    }

	public function getPostDetails($post_uid, $user_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE post_uid = :post_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_STR);
    	
         if($statement->execute()){
    		$post_details = $statement->fetch(PDO::FETCH_ASSOC);

        	return $post_details;
        }
		else{
			return false;
		}

    }
    
    public function getFlagDetails($flag_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "SELECT * FROM Flags WHERE flag_uid = :flag_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':flag_uid', $flag_uid, PDO::PARAM_STR);
    	
        if($statement->execute()){
    		$flag_details = $statement->fetch(PDO::FETCH_ASSOC);
        	return $flag_details;
        }
		else{
			return false;
		}

    }

    
    public function getEventDetails($event_uid, $user_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE event_uid = :event_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':event_uid', $event_uid, PDO::PARAM_STR);
        
        if($statement->execute()){
        	$event_details = $statement->fetch(PDO::FETCH_ASSOC);

        	return $event_details;
        }
		else{
			return false;
		}

    }


	 public function getPostImages($post_uid, $tag, $pdo = null, $cursor = null){
	 	
	 	if($pdo == null){
			$pdo = $this->newConnection();
		}

        $query = "SELECT * FROM Images WHERE tag = :tag AND post_uid = :post_uid ORDER BY created_at ASC";
        $statement = $pdo->prepare($query);
        //$statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
      
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_INT);
        $statement->bindValue(':tag', $tag, PDO::PARAM_STR);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            array_push($rows, $row);
            $cursor = $row['created_at'];
        }
      
        return array(
            'image' => $rows,
            'cursor' => $cursor,
        );

    }
    
    public function getPostLikes($post_uid, $user_uid, $tag, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT * FROM Likes WHERE Likes.post_uid = :post_uid AND Likes.tag = :tag AND created_at < :cursor ORDER BY Likes.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT * FROM Likes WHERE Likes.post_uid = :post_uid AND Likes.tag = :tag ORDER BY Likes.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', 21, PDO::PARAM_INT);
        $statement->bindValue(':tag', $tag, PDO::PARAM_STR);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          
            $detail = $this->rp->getLikeFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
      
        
        return array(
            'like' => $rows,
            'cursor' => $cursor,
        );

    }
    
    public function getPostComments($post_uid, $user_uid, $tag, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT * FROM Comments WHERE Comments.post_uid = :post_uid AND Comments.tag = :tag AND created_at < :cursor ORDER BY Comments.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT * FROM Comments WHERE Comments.post_uid = :post_uid AND Comments.tag = :tag ORDER BY Comments.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->bindValue(':tag', $tag, PDO::PARAM_STR);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getCommentFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
        
		$reversed = array_reverse($rows);
		
        return array(
            'comment' => $reversed,
            'cursor' => $cursor,
        );

    }
    
    public function newPost($user_uid, $poster_uid, $description, $lat, $lng, $image_count, $address, $visibility, $location_type, $post_type, $checkin_page_uid = "NULL", $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "INSERT INTO Posts (user_uid, description, latitude, longitude, image_count, address, visibility, location_type, type, checkin_page_uid) VALUES (:user_uid, :description, :latitude, :longitude, :image_count, :address, :visibility, :location_type, :type, :checkin_page_uid)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $poster_uid, PDO::PARAM_STR);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->bindValue(':latitude', $lat, PDO::PARAM_STR);
        $statement->bindValue(':longitude', $lng, PDO::PARAM_STR);
        $statement->bindValue(':image_count', $image_count, PDO::PARAM_INT);
        $statement->bindValue(':address', $address, PDO::PARAM_STR);
        $statement->bindValue(':visibility', $visibility, PDO::PARAM_STR);
        $statement->bindValue(':location_type', $location_type, PDO::PARAM_STR);
        $statement->bindValue(':type', $post_type, PDO::PARAM_STR);
        $statement->bindValue(':checkin_page_uid', $checkin_page_uid, PDO::PARAM_STR);
        $statement->execute();
        
        $post_uid = $pdo->lastInsertId();
        
		$post_details = $this->getPostDetails($post_uid, $user_uid, $pdo);
	    
	    if($post_type == 'user'){
                $this->incrementUserPostCount($poster_uid, $pdo);
        }
        else{
                $this->incrementPagePostCount($poster_uid, $pdo);
        }
        
        if($checkin_page_uid != "NULL" && $checkin_page_uid != $poster_uid){
        	$this->incrementPageCheckInCount($checkin_page_uid, $pdo);
        }
	  	
	  	return $post_details;
	    
    }
    
    public function deletePost($post_uid, $user_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
    	$post_details = $this->getPostDetails($post_uid, $user_uid, $pdo);
    	
        $sql = "UPDATE Posts SET deleted = TRUE WHERE post_uid = :post_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_STR);
        
        if($statement->execute()){
        	
        	if($post_details['type'] == 'user'){
                $this->decrementUserPostCount($post_details['user_uid'], $pdo);
	        }
	        else{
	             $this->decrementPagePostCount($post_details['user_uid'], $pdo);
	        }
        	
			return true;
		}

		return false;
    	
    }
    
    public function newFlag($user_uid, $description, $lat, $lng, $address, $checkin_page_uid = "NULL", $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "INSERT INTO Flags (user_uid, description, latitude, longitude, address, checkin_page_uid) VALUES (:user_uid, :description, :latitude, :longitude, :address, :checkin_page_uid)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->bindValue(':latitude', $lat, PDO::PARAM_STR);
        $statement->bindValue(':longitude', $lng, PDO::PARAM_STR);
        $statement->bindValue(':checkin_page_uid', $checkin_page_uid, PDO::PARAM_STR);
        $statement->bindValue(':address', $address, PDO::PARAM_STR);
        $statement->execute();
        
        $flag_uid = $pdo->lastInsertId();
        
		$flag_details = $this->getFlagDetails($flag_uid, $pdo);
		
		if($checkin_page_uid != "NULL"){
        	$this->incrementPageCheckInCount($checkin_page_uid, $pdo);
        }
	  	
	  	return $flag_details;
	    
    }
    
	 public function deleteFlag($flag_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Flags SET deleted = TRUE WHERE flag_uid = :flag_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':flag_uid', $flag_uid, PDO::PARAM_STR);
        
        if($statement->execute()){
        	
			return true;
		}

		return false;
    	
    }

    public function newEvent($user_uid, $poster_uid, $title, $description, $start_date, $end_date, $start_time, $end_time, $lat, $lng, $image_count, $address, $visibility, $organiser_type, $checkin_page_uid = "NULL", $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "INSERT INTO Events (organiser_uid, title, description, start_date, end_date, start_time, end_time, latitude, longitude, image_count, address, visibility, organiser_type, checkin_page_uid) VALUES (:organiser_uid, :title, :description, :start_date, :end_date, :start_time, :end_time, :latitude, :longitude, :image_count, :address, :visibility, :organiser_type, :checkin_page_uid)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':organiser_uid', $poster_uid, PDO::PARAM_STR);
        $statement->bindValue(':title', $title, PDO::PARAM_STR);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->bindValue(':start_time', $start_time, PDO::PARAM_STR);
        $statement->bindValue(':end_time', $end_time, PDO::PARAM_STR);
        $statement->bindValue(':start_date', $start_date, PDO::PARAM_STR);
        $statement->bindValue(':end_date', $end_date, PDO::PARAM_STR);
        $statement->bindValue(':latitude', $lat, PDO::PARAM_STR);
        $statement->bindValue(':longitude', $lng, PDO::PARAM_STR);
        $statement->bindValue(':image_count', $image_count, PDO::PARAM_INT);
        $statement->bindValue(':address', $address, PDO::PARAM_STR);
        $statement->bindValue(':visibility', $visibility, PDO::PARAM_STR);
        $statement->bindValue(':organiser_type', $organiser_type, PDO::PARAM_STR);
        $statement->bindValue(':checkin_page_uid', $checkin_page_uid, PDO::PARAM_STR);
        $statement->execute();
        
        $event_uid = $pdo->lastInsertId();
        
		$event_details = $this->getEventDetails($event_uid, $user_uid, $pdo);
		
		if($checkin_page_uid != "NULL" && $checkin_page_uid != $poster_uid){
        	$this->incrementPageCheckInCount($checkin_page_uid, $pdo);
        }
	  	
	  	return $event_details;

    }
    
    public function deleteEvent($event_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Events SET deleted = TRUE WHERE event_uid = :event_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':event_uid', $event_uid, PDO::PARAM_STR);
        
        if($statement->execute()){
        	
			return true;
		}

		return false;
    	
    }


    public function newPage($user_uid, $title, $description, $lat, $lng, $profile_image_url, $banner_images, $address, $category, $editable_by_others, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "INSERT INTO Pages (user_uid, title, description, latitude, longitude, address, profile_image, banner_images, category, editable_by_others) VALUES (:user_uid, :title, :description, :latitude, :longitude, :address, :profile_image, :banner, :category, :editable)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        $statement->bindValue(':title', $title, PDO::PARAM_STR);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->bindValue(':profile_image', $profile_image_url, PDO::PARAM_STR);
        $statement->bindValue(':banner', $banner_images, PDO::PARAM_STR);
        $statement->bindValue(':latitude', $lat, PDO::PARAM_STR);
        $statement->bindValue(':longitude', $lng, PDO::PARAM_STR);
        $statement->bindValue(':address', $address, PDO::PARAM_STR);
        $statement->bindValue(':category', $category, PDO::PARAM_STR);
        $statement->bindValue(':editable', $editable_by_others, PDO::PARAM_STR);
        $statement->execute();
        
        $page_uid = $pdo->lastInsertId();
        
		$page_details = $this->getPageDetails($page_uid, $user_uid, $pdo);
	    
		return $page_details;

    }
    
    
     public function deletePage($page_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Pages SET deleted = TRUE WHERE page_uid = :page_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
        
        if($statement->execute()){
			return true;
		}

		return false;
    	
    }


	public function editPage($page_uid, $user_uid, $title, $description, $lat, $lng, $profile_image_url, $banner_images, $address, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Pages SET title = :title, description = :description, latitude = :latitude, longitude = :longitude, address = :address, profile_image = :profile_image, banner_images = :banner WHERE page_uid = :page_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
        $statement->bindValue(':title', $title, PDO::PARAM_STR);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->bindValue(':profile_image', $profile_image_url, PDO::PARAM_STR);
        $statement->bindValue(':banner', $banner_images, PDO::PARAM_STR);
        $statement->bindValue(':latitude', $lat, PDO::PARAM_STR);
        $statement->bindValue(':longitude', $lng, PDO::PARAM_STR);
        $statement->bindValue(':address', $address, PDO::PARAM_STR);
        $statement->execute();
        
		$page_details = $this->getPageDetails($page_uid, $user_uid, $pdo);
	    
		return $page_details;

    }

	public function editEvent($event_uid, $user_uid, $title, $description, $lat, $lng, $address, $start_time, $end_time, $start_date, $end_date, $checkin_page_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Events SET title = :title, description = :description, latitude = :latitude, longitude = :longitude, address = :address, start_time = :start_time, end_time = :end_time, start_date = :start_date, end_date = :end_date, checkin_page_uid = :checkin_page_uid WHERE event_uid = :event_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':event_uid', $event_uid, PDO::PARAM_STR);
        $statement->bindValue(':title', $title, PDO::PARAM_STR);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->bindValue(':latitude', $lat, PDO::PARAM_STR);
        $statement->bindValue(':longitude', $lng, PDO::PARAM_STR);
        $statement->bindValue(':address', $address, PDO::PARAM_STR);
        $statement->bindValue(':start_time', $start_time, PDO::PARAM_STR);
        $statement->bindValue(':end_time', $end_time, PDO::PARAM_STR);
        $statement->bindValue(':start_date', $start_date, PDO::PARAM_STR);
        $statement->bindValue(':end_date', $end_date, PDO::PARAM_STR);
        $statement->bindValue(':checkin_page_uid', $checkin_page_uid, PDO::PARAM_STR);
        $statement->execute();
        
		$event_details = $this->getEventDetails($event_uid, $user_uid, $pdo);
	    
		return $event_details;

    }
    
    public function editFlag($flag_uid, $user_uid, $description, $lat, $lng, $address, $checkin_page_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Flags SET description = :description, latitude = :latitude, longitude = :longitude, address = :address, checkin_page_uid = :checkin_page_uid WHERE flag_uid = :flag_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':flag_uid', $flag_uid, PDO::PARAM_STR);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->bindValue(':latitude', $lat, PDO::PARAM_STR);
        $statement->bindValue(':longitude', $lng, PDO::PARAM_STR);
        $statement->bindValue(':checkin_page_uid', $checkin_page_uid, PDO::PARAM_STR);
        $statement->bindValue(':address', $address, PDO::PARAM_STR);
        $statement->execute();
        
		$flag_details = $this->getFlagDetails($flag_uid, $pdo);
	    
		return $flag_details;

    }


	public function addImageToPost($post_uid, $image_url, $description, $image_type, $width, $height, $tag, $pdo = null){

		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "INSERT INTO Images (description, post_uid, type, width, height, path, tag) VALUES (:description, :post_uid, :type, :width, :height, :path, :tag)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_STR);
        $statement->bindValue(':type', $image_type, PDO::PARAM_STR);
        $statement->bindValue(':width', $width, PDO::PARAM_INT);
        $statement->bindValue(':height', $height, PDO::PARAM_INT);
        $statement->bindValue(':path', $image_url, PDO::PARAM_STR);
        $statement->bindValue(':tag', $tag, PDO::PARAM_STR);
        $statement->execute();
        
        $image_uid = $pdo->lastInsertId();
        
    	return $image_uid;
    }
    
    public function removeImageFromPost($image_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "DELETE FROM Images WHERE image_uid = :image_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':image_uid', $image_uid, PDO::PARAM_STR);
        
        return $statement->execute();

    }
    
    
    public function postLike($user_uid, $post_uid, $tag, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "INSERT INTO Likes (user_uid, post_uid, tag) VALUES (:user_uid, :post_uid, :tag)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_STR);
        $statement->bindValue(':tag', $tag, PDO::PARAM_STR);
        
        if($statement->execute()){
        
	        if($tag == 'post'){
				$this->incrementPostLikeCount($post_uid, $pdo);
				$post = $this->getPostDetails($post_uid, $user_uid, $pdo);
	
				if($post['type'] == 'user'){
					if($user_uid != $post['user_uid']){
						$this->storeNotification($user_uid, $post['user_uid'], 'like', 'user', $pdo, $post_uid, 'post');
					}
				}
				else{
					$page = $this->getPageDetails($post['user_uid'], $user_uid, $pdo);
					if($user_uid != $page['user_uid']){
						$this->storeNotification($user_uid, $page['user_uid'], 'like', 'user', $pdo, $post_uid, 'post');
					}
				}
			}
			else if($tag == 'event'){
				$this->incrementEventLikeCount($post_uid, $pdo);
				$event = $this->getEventDetails($post_uid, $user_uid, $pdo);
	
				if($event['organiser_type'] == 'user'){
					if($user_uid != $event['organiser_uid']){
						$this->storeNotification($user_uid, $event['organiser_uid'], 'like', 'user', $pdo, $post_uid, 'event');
					}
				}
				else{
					$page = $this->getPageDetails($event['organiser_uid'], $user_uid, $pdo);
					if($user_uid != $page['user_uid']){
						$this->storeNotification($user_uid, $page['user_uid'], 'like', 'user', $pdo, $post_uid, 'event');
					}
				}
			}
	     
	     	return true;
	    }
	    else{
	    	return false;
	    }

    }
    	


    public function postUnlike($user_uid, $post_uid, $tag, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "DELETE FROM Likes WHERE post_uid = :post_uid AND user_uid = :user_uid AND tag = :tag LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_STR);
        $statement->bindValue(':tag', $tag, PDO::PARAM_STR);
        
        if($statement->execute()){
        	
        	if($tag == 'post'){
				$this->decrementPostLikeCount($post_uid, $pdo);
			}
			else if($tag == 'event'){
				$this->decrementEventLikeCount($post_uid, $pdo);
			}
			
			return true;
		}

		return false;
       
    }
	

	public function postComment($user_uid, $post_uid, $description, $tag, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "INSERT INTO Comments (user_uid, post_uid, description, tag) VALUES (:user_uid, :post_uid, :description, :tag)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_STR);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->bindValue(':tag', $tag, PDO::PARAM_STR);

		if($statement->execute()){
        
	        if($tag == 'post'){
				$this->incrementPostCommentCount($post_uid, $pdo);
				$post = $this->getPostDetails($post_uid, $user_uid, $pdo);
	
				if($post['type'] == 'user'){
					if($user_uid != $post['user_uid']){
						$this->storeNotification($user_uid, $post['user_uid'], 'comment', 'user', $pdo, $post_uid, 'post');
					}
				}
				else{
					$page = $this->getPageDetails($post['user_uid'], $user_uid, $pdo);
					if($user_uid != $page['user_uid']){
						$this->storeNotification($user_uid, $page['user_uid'], 'comment', 'user', $pdo, $post_uid, 'post');
					}
				}
			}
			else if($tag == 'event'){
				$this->incrementEventCommentCount($post_uid, $pdo);
				$event = $this->getEventDetails($post_uid, $user_uid, $pdo);
	
				if($event['organiser_type'] == 'user'){
					if($user_uid != $event['organiser_uid']){
						$this->storeNotification($user_uid, $event['organiser_uid'], 'comment', 'user', $pdo, $post_uid, 'event');
					}
				}
				else{
					$page = $this->getPageDetails($event['organiser_uid'], $user_uid, $pdo);
					if($user_uid != $page['user_uid']){
						$this->storeNotification($user_uid, $page['user_uid'], 'comment', 'user', $pdo, $post_uid, 'event');
					}
				}
			}
	     
	     	return true;
	    }
	    else{
	    	return false;
	    }
    	
    }
    
    public function isUserLikedPost($post_uid, $user_uid, $tag, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
    	$rows = $pdo->query("SELECT count(*) FROM Likes WHERE tag = '$tag' AND user_uid = '$user_uid' AND post_uid = '$post_uid' LIMIT 1")->fetchColumn(); 
		
		if($rows > 0){
			return true;
		}
		
		return false;
    	
    }
    
    public function isUserAttendedEvent($event_uid, $user_uid, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
    	$rows = $pdo->query("SELECT count(*) FROM EventAttenders WHERE user_uid = '$user_uid' AND event_uid = '$event_uid'LIMIT 1")->fetchColumn(); 
		
		if($rows > 0){
			return true;
		}
		
		return false;
    	
    }
    
    
    public function addEventAttender($event_uid, $user_uid, $attender_status, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "INSERT INTO EventAttenders (event_uid, user_uid, status) VALUES (:event_uid, :user_uid, :status)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':event_uid', $event_uid, PDO::PARAM_STR);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        $statement->bindValue(':status', $attender_status, PDO::PARAM_STR);
    	
    	if($statement->execute()){
        	return true;
        }
        else{
        	return false;
        }

    }
    
    
    public function updateEventAttender($event_uid, $user_uid, $attender_status, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE EventAttenders SET status = :status WHERE event_uid = :event_uid AND user_uid = :user_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':event_uid', $event_uid, PDO::PARAM_STR);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        $statement->bindValue(':status', $attender_status, PDO::PARAM_STR);
        
        if($statement->execute()){
        	return true;
        }
        else{
        	return false;
        }

    }


	public function getEventAttenders($event_uid, $cursor = null, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT EventAttenders.attender_uid, EventAttenders.created_at AS attender_created_at, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM EventAttenders, Users WHERE EventAttenders.event_uid = :event_uid AND EventAttenders.user_uid = Users.user_uid AND EventAttenders.created_at < :cursor ORDER BY EventAttenders.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT EventAttenders.attender_uid, EventAttenders.created_at AS attender_created_at, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM EventAttenders, Users WHERE EventAttenders.event_uid = :event_uid AND EventAttenders.user_uid = Users.user_uid ORDER BY EventAttenders.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

      
        $statement->bindValue(':event_uid', $event_uid, PDO::PARAM_INT);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        	
            $detail = $this->rp->getUserFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
       

        return array(
            'event_attender' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    public function getEventAttenderCount($event_uid, $pdo = null){
    	
    	if($pdo == null){
	    	$pdo = $this->newConnection();
		}

        $sql = "SELECT (SELECT COUNT(attender_uid) FROM EventAttenders WHERE event_uid = $event_uid AND status = 'going') AS going, (SELECT COUNT(attender_uid) FROM EventAttenders WHERE event_uid = $event_uid AND status = 'interested') AS interested";
        $result = $pdo->query($sql);
        
		return $result->fetch();
    }
    
    public function getUserAttendStatus($event_uid, $user_uid, $pdo = null){
    	
    	if($pdo == null){
	    	$pdo = $this->newConnection();
		}

        $sql = "SELECT status FROM EventAttenders WHERE event_uid = $event_uid AND user_uid = $user_uid LIMIT 1";
        $result = $pdo->query($sql);
        
        while ($row = $result->fetch()) {
			return $row['status'];
		}
		return 'normal';
    }

    public function getSpecificEventAttenders($event_uid, $attender_status, $user_uid, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT EventAttenders.attender_uid, EventAttenders.created_at AS attender_created_at, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM EventAttenders, Users WHERE EventAttenders.event_uid = :event_uid AND EventAttenders.status = :status AND EventAttenders.user_uid = Users.user_uid AND EventAttenders.created_at < :cursor ORDER BY EventAttenders.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT EventAttenders.attender_uid, EventAttenders.created_at AS attender_created_at, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM EventAttenders, Users WHERE EventAttenders.event_uid = :event_uid AND EventAttenders.status = :status AND EventAttenders.user_uid = Users.user_uid ORDER BY EventAttenders.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':event_uid', $event_uid, PDO::PARAM_INT);
        $statement->bindValue(':status', $attender_status, PDO::PARAM_STR);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getUserFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
       
        
        return array(
            'event_attender' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    public function searchSpecificEventAttenders($event_uid, $attender_status, $user_uid, $keyword, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT EventAttenders.attender_uid, EventAttenders.created_at AS attender_created_at, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM EventAttenders, Users WHERE EventAttenders.event_uid = :event_uid AND EventAttenders.status = :status AND (LOWER(Users.name) LIKE :keyword1 OR LOWER(Users.id) LIKE :keyword2) AND EventAttenders.user_uid = Users.user_uid AND EventAttenders.created_at < :cursor ORDER BY EventAttenders.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT EventAttenders.attender_uid, EventAttenders.created_at AS attender_created_at, Users.user_uid, Users.id, Users.name, Users.profile_image, Users.profile_image_small, Users.registered_at, Users.post_count FROM EventAttenders, Users WHERE EventAttenders.event_uid = :event_uid AND EventAttenders.status = :status AND (LOWER(Users.name) LIKE :keyword1 OR LOWER(Users.id) LIKE :keyword2) AND EventAttenders.user_uid = Users.user_uid ORDER BY EventAttenders.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':event_uid', $event_uid, PDO::PARAM_INT);
        $statement->bindValue(':status', $attender_status, PDO::PARAM_STR);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getUserFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
       
        
        return array(
            'event_attender' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    
    public function searchUsers($keyword, $user_uid, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT * FROM Users WHERE (LOWER(name) LIKE :keyword1 OR LOWER(id) LIKE :keyword2) AND user_uid > :cursor ORDER BY user_uid ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT * FROM Users WHERE (LOWER(name) LIKE :keyword1 OR LOWER(id) LIKE :keyword2) ORDER BY user_uid ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getUserFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['user_uid'];
        }
       

        return array(
            'user' => $rows,
            'cursor' => $cursor,
        );
    	
    }


	public function searchPages($keyword, $category, $user_uid, $cursor_checkin = null, $cursor_date = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor_date != null){
	        $query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE (LOWER(Pages.title) LIKE :keyword1 OR LOWER(Pages.description) LIKE :keyword2 OR LOWER(Pages.address) LIKE :keyword3) AND Pages.category LIKE :category AND ((Pages.checkin_count = :cursor_checkin AND Pages.created_at < :cursor_date) OR Pages.checkin_count < :cursor_checkin2) ORDER BY Pages.checkin_count DESC, Pages.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor_checkin', $cursor_checkin, PDO::PARAM_INT);
	        $statement->bindValue(':cursor_checkin2', $cursor_checkin, PDO::PARAM_INT);
	        $statement->bindValue(':cursor_date', $cursor_date, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Pages.*, (SELECT COUNT(request_uid) FROM Requests WHERE (to_user_uid = Pages.page_uid AND from_user_uid = $user_uid AND type = 'page') LIMIT 1) AS followed FROM Pages WHERE (LOWER(Pages.title) LIKE :keyword1 OR LOWER(Pages.description) LIKE :keyword2 OR LOWER(Pages.address) LIKE :keyword3) AND Pages.category LIKE :category ORDER BY Pages.checkin_count DESC, Pages.created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

       
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':keyword3', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':category', "%$category%", PDO::PARAM_STR);  
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getPageFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor_checkin = $row['checkin_count'];
            $cursor_date = $row['created_at'];
        }
      

        return array(
            'page' => $rows,
            'cursor_checkin' => $cursor_checkin,
            'cursor_date' => $cursor_date,
        );
    	
    }
    
    public function searchPosts($keyword, $user_uid, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE (LOWER(description) LIKE :keyword1 OR LOWER(address) LIKE :keyword2) AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Posts.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Posts.post_uid AND tag = 'post') LIMIT 1) AS liked FROM Posts WHERE (LOWER(description) LIKE :keyword1 OR LOWER(address) LIKE :keyword2) AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

      	$statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getPostFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
      

        return array(
            'post' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
     public function searchEvents($keyword, $user_uid, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) AND end_date >= NOW() AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) AND created_at < :cursor ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT Events.*, (SELECT COUNT(like_uid) FROM Likes WHERE (user_uid = $user_uid AND post_uid = Events.event_uid AND tag = 'event') LIMIT 1) AS liked FROM Events WHERE (LOWER(title) LIKE :keyword1 OR LOWER(description) LIKE :keyword2 OR LOWER(address) LIKE :keyword3) AND end_date >= NOW() AND (visibility = 'public' OR user_uid = $user_uid OR user_uid IN (SELECT Users.user_uid FROM Requests, Users WHERE Requests.type = 'user' AND Requests.status = 'accepted' AND ((Requests.to_user_uid = $user_uid AND Requests.from_user_uid = Users.user_uid) OR (Requests.from_user_uid = $user_uid AND Requests.to_user_uid = Users.user_uid)))) ORDER BY created_at DESC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':keyword2', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':keyword3', "%$keyword%", PDO::PARAM_STR); 
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getEventFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['created_at'];
        }
       

        return array(
            'event' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    public function searchFlags($keyword, $user_uid, $cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT * FROM Flags WHERE user_uid = $user_uid AND LOWER(description) LIKE :keyword1 AND flag_uid > :cursor ORDER BY flag_uid ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
	    	$query = "SELECT * FROM Flags WHERE user_uid = $user_uid AND LOWER(description) LIKE :keyword1 ORDER BY flag_uid ASC LIMIT :limit";
	        $statement = $pdo->prepare($query);
	    }

        $statement->bindValue(':keyword1', "%$keyword%", PDO::PARAM_STR);
        $statement->bindValue(':limit', $this->limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = array();
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            $detail = $this->rp->getFlagFromQuery($row, $this, $user_uid, $pdo);
            array_push($rows, $detail);
            $cursor = $row['flag_uid'];
        }
      
        return array(
            'flag' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
    
    public function accessCheck($user_uid, $token, $pdo = null){

		if($pdo == null){
			$pdo = $this->newConnection();
		}
		    	
    	$rows = $pdo->query("SELECT count(*) FROM Users WHERE user_uid = '$user_uid' AND token = '$token' LIMIT 1")->fetchColumn(); 
		
		if($rows > 0){
			return true;
		}
		
		return false;
    	
    }

	public function incrementPostLikeCount($post_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}

        $sql = "UPDATE Posts SET like_count = like_count + 1 WHERE post_uid = :post_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_STR);
        
        return $statement->execute();
   
	}
	 
	public function decrementPostLikeCount($post_uid, $pdo = null){
	 	
	 	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Posts SET like_count = like_count - 1 WHERE post_uid = :post_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 	
	}
	
	public function incrementPostCommentCount($post_uid, $pdo = null){
	 	
	 	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Posts SET comment_count = comment_count + 1 WHERE post_uid = :post_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 	
	}
	
	public function incrementEventLikeCount($event_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Events SET like_count = like_count + 1 WHERE event_uid = :event_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':event_uid', $event_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 
	}
	
	public function decrementEventLikeCount($event_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Events SET like_count = like_count - 1 WHERE event_uid = :event_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':event_uid', $event_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 
	}
	
	public function incrementEventCommentCount($event_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Events SET comment_count = comment_count + 1 WHERE event_uid = :event_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':event_uid', $event_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 	
	}


	public function incrementUserPostCount($user_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Users SET post_count = post_count + 1 WHERE user_uid = :user_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 	
	}
	
	public function decrementUserPostCount($user_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Users SET post_count = post_count - 1 WHERE user_uid = :user_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 	
	}
	
	public function incrementUserFriendCount($user_uid, $pdo = null){
	 	
	 	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Users SET friend_count = friend_count + 1 WHERE user_uid = :user_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 	
	 	
	}
	
	public function decrementUserFriendCount($user_uid, $pdo = null){
	 	
	 	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Users SET friend_count = friend_count - 1 WHERE user_uid = :user_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 	
	}
	
	public function incrementPageCheckInCount($page_uid, $pdo = null){
	 	
	 	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Pages SET checkin_count = checkin_count + 1 WHERE page_uid = :page_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 	
	}
	
	public function incrementPagePostCount($page_uid, $pdo = null){
	 	
	 	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Pages SET post_count = post_count + 1 WHERE page_uid = :page_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 	
	}
	
	public function decrementPagePostCount($page_uid, $pdo = null){
	 	
	 	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Pages SET post_count = post_count - 1 WHERE page_uid = :page_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 	
	}
	
	public function incrementPageFollowerCount($page_uid, $pdo = null){
	 	
	 	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Pages SET follower_count = follower_count + 1 WHERE page_uid = :page_uid";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 	
	}
	
	public function decrementPageFollowerCount($page_uid, $pdo = null){
	 	
	 	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "UPDATE Pages SET follower_count = follower_count - 1 WHERE page_uid = :page_uid LIMIT 1";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':page_uid', $page_uid, PDO::PARAM_STR);
        
        return $statement->execute();
	 	
	}
	
	public function storeReport($user_uid, $post_uid, $tag, $description, $category, $pdo = null){
	 	
	 	if($pdo == null){
			$pdo = $this->newConnection();
		}
		
        $sql = "INSERT INTO Reports (user_uid, post_uid, tag, description, category) VALUES (:user_uid, :post_uid, :tag, :description, :category)";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
        $statement->bindValue(':post_uid', $post_uid, PDO::PARAM_STR);
        $statement->bindValue(':tag', $tag, PDO::PARAM_STR);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->bindValue(':category', $category, PDO::PARAM_STR);
        
        return $statement->execute();
	}
	
	public function getReportCount($post_uid, $tag, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
    	$rows = $pdo->query("SELECT count(*) FROM Reports WHERE tag = '$tag' AND post_uid = '$post_uid' LIMIT 1")->fetchColumn(); 
       
		return $rows;
	}
	
	public function isExistingPageCategory($category_name, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
		$sql = "SELECT count(*) FROM PageCategories WHERE name = :category_name LIMIT 1";
    	$statement = $pdo->prepare($sql);
    	$statement->bindValue(':category_name', $category_name, PDO::PARAM_STR);
    	
		$statement->execute();
    	$rows = $statement->fetchColumn();
		
		if($rows > 0){
			return true;
		}
		
		return false;
		
	}
	
	public function getPageCategory($category_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
		$sql = "SELECT * FROM PageCategories WHERE category_uid = :category_uid LIMIT 1";
		$statement = $pdo->prepare($sql);
		$statement->bindValue(':category_uid', $category_uid, PDO::PARAM_STR);
     
        if($statement->execute()){
    		$category = $statement->fetch(PDO::FETCH_ASSOC);
    		
        	return $category;
        }
		else{
			return false;
		}
		
	}
	
	public function getPageCategories($cursor = null, $pdo = null){
    	
    	if($pdo == null){
			$pdo = $this->newConnection();
		}

		if($cursor != null){
	        $query = "SELECT * FROM PageCategories WHERE category_uid > :cursor LIMIT 10";
	        $statement = $pdo->prepare($query);
	        $statement->bindValue(':cursor', $cursor, PDO::PARAM_STR);
	    }
	    else{
			$query = "SELECT * FROM PageCategories LIMIT 20";
	        $statement = $pdo->prepare($query);
	    }

        $statement->execute();
        $rows = array();
       
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
            array_push($rows, $row);
            $cursor = $row['category_uid'];
        }
      

        return array(
            'category' => $rows,
            'cursor' => $cursor,
        );
    	
    }
    
	
	public function storeNewPageCategory($category_name, $user_uid, $pdo = null){
		
		if($pdo == null){
			$pdo = $this->newConnection();
		}
		
		$existed = $this->isExistingPageCategory($category_name, $pdo);
		
		if($existed){
			return true;
		}
		else{
			$sql = "INSERT INTO PageCategories (name, user_uid) VALUES (:name, :user_uid)";
	        $statement = $pdo->prepare($sql);
	        $statement->bindValue(':user_uid', $user_uid, PDO::PARAM_STR);
	        $statement->bindValue(':name', $category_name, PDO::PARAM_STR);
	        
	        return $statement->execute();
	    }

		
	}
	
	public function getPDO(){
		
		$pdo = $this->newConnection();
		
		return $pdo;
	}
    
    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array('salt' => $salt, 'password' => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }

	public function generateUID($prefix){
        $uid = uniqid($prefix, true);
		$uid_final = str_replace(".","_",$uid);
        return $uid_final;
    }

	public function generateToken($prefix){
        $token = uniqid($prefix, true);
        return $token;
    }

    

    public static function getMysqlDsn($dbName, $port, $connectionName = null)
    {
        if ($connectionName) {
            return sprintf('mysql:unix_socket=/cloudsql/%s;dbname=%s',
                $connectionName,
                $dbName);
        }

        return sprintf('mysql:host=127.0.0.1;port=%s;dbname=%s', $port, $dbName);
    }

    public static function getPostgresDsn($dbName, $port, $connectionName = null)
    {
        if ($connectionName) {
            return sprintf('pgsql:host=/cloudsql/%s;dbname=%s',
                $connectionName,
                $dbName);
        }

        return sprintf('pgsql:host=127.0.0.1;port=%s;dbname=%s', $port, $dbName);
    }
    
    
}
