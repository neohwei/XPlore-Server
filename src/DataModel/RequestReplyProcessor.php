<?php

namespace Google\Cloud\Samples\Bookshelf\DataModel;

class RequestReplyProcessor
{

    public function __construct(){
    }

    public function getPostFromQuery($row, $db, $user_uid, $pdo = null){

        	$post = array();
            $post['post_uid'] = $row['post_uid'];
            $post['description'] = $row['description'];
            $post['address'] = $row['address'];
            $post['created_at'] = $row['created_at'];
		    $post['image_count'] = $row['image_count'];
            $post['type'] = $row['type'];
            $post['visibility'] = $row['visibility'];
            $post['location_type'] = $row['location_type'];
            $post['checkin_page_uid'] = $row['checkin_page_uid'];
            $post['like_count'] = $row['like_count'];
            $post['comment_count'] = $row['comment_count'];
            $post['liked_by_user'] = $row['liked'] > 0;
            $post['deleted'] = $row['deleted'];
            
            $post['report_count'] = $db->getReportCount($post['post_uid'], 'post', $pdo);
            
            if($row['location_type'] == 'show'){
            	$post['latitude'] = $row['latitude'];
            	$post['longitude'] = $row['longitude'];
            }

        	if($row['type'] == 'page'){
        		        
        		$sql_page = $db->getPageDetails($row['user_uid'], $user_uid, $pdo);
        		        
        		$post['page'] = $this->getPageFromQuery($sql_page, $db, $user_uid, $pdo);		
        	

        	}else{
        		        
        		$sql_user = $db->getUserProfile($row['user_uid'], $user_uid, $pdo);
        		        
        		$post['user'] = $this->getUserFromQuery($sql_user, $db, $user_uid, $pdo);
        		
        	}
        	
        	if($row['checkin_page_uid'] != 'NULL'){
        			
        		$checkin_page = $db->getPageDetails($row['checkin_page_uid'], $user_uid, $pdo);
        			
        		$post['checkin_page'] = $this->getPageFromQuery($checkin_page, $db, $user_uid, $pdo);
        	}
             
	        $images = $db->getPostImages($post['post_uid'], 'post', $pdo);
	        $post['image'] = $images['image'];

		    return $post;
    }

    public function getEventFromQuery($row, $db, $user_uid, $pdo = null){

        	$event = array();
            $event['event_uid'] = $row['event_uid'];
		    $event['title'] = $row['title'];
            $event['description'] = $row['description'];
			$event['start_date'] = $row['start_date'];
			$event['end_date'] = $row['end_date'];
			$event['start_time'] = $row['start_time'];
			$event['end_time'] = $row['end_time'];
            $event['latitude'] = $row['latitude'];
            $event['longitude'] = $row['longitude'];
            $event['address'] = $row['address'];
            $event['created_at'] = $row['created_at'];
			$event['image_count'] = $row['image_count'];
			$event['organiser_uid'] = $row['organiser_uid'];
            $event['organiser_type'] = $row['organiser_type'];
            $event['checkin_page_uid'] = $row['checkin_page_uid'];
            $event['visibility'] = $row['visibility'];
            $event['like_count'] = $row['like_count'];
            $event['comment_count'] = $row['comment_count'];
            $event['liked_by_user'] = $row['liked'] > 0;
            $event['deleted'] = $row['deleted'];
            
            $event['report_count'] = $db->getReportCount($event['event_uid'], 'event', $pdo);

        	if($row['organiser_type'] == 'page'){
        		        
        		    $sql_page = $db->getPageDetails($row['organiser_uid'], $user_uid, $pdo);
        		        
        			$event['page'] = $this->getPageFromQuery($sql_page, $db, $user_uid, $pdo);		
        		
        	}else{
        		        
        		    $sql_user = $db->getUserProfile($row['organiser_uid'], $user_uid, $pdo);
        		        
        			$event['user'] = $this->getUserFromQuery($sql_user, $db, $user_uid, $pdo);
        			
        	}
        	
        	if($row['checkin_page_uid'] != 'NULL'){
        			
	        		$checkin_page = $db->getPageDetails($row['checkin_page_uid'], $user_uid, $pdo);
	        			
	        		$event['checkin_page'] = $this->getPageFromQuery($checkin_page, $db, $user_uid, $pdo);
	        }
             
            $attender_count = $db->getEventAttenderCount($event['event_uid'], $pdo);
            $event['interested'] = $attender_count['interested'];
            $event['going'] = $attender_count['going'];
            
            $event['status'] = $db->getUserAttendStatus($event['event_uid'], $user_uid, $pdo);
               
	        $images = $db->getPostImages($row['event_uid'], 'event', $pdo);
	        $event['image'] = $images['image'];


		    return $event;
    }
    
     public function getFlagFromQuery($row, $db, $user_uid, $pdo = null){

		$images = $db->getPostImages($row['flag_uid'], 'flag', $pdo);
		
		if($row['checkin_page_uid'] != 'NULL'){	
	        $checkin_page = $db->getPageDetails($row['checkin_page_uid'], $user_uid, $pdo);
	        $row['checkin_page'] = $this->getPageFromQuery($checkin_page, $db, $user_uid, $pdo);
	    }
		
		$row['image'] = $images['image'];
		
		return $row;	

    }


    public function getAttenderFromQuery($row, $db, $user_uid){

		$attender = array();
		$attender['attender_uid'] = $row['attender_uid'];
		$attender['user_uid'] = $row['user_uid'];
		$attender['event_uid'] = $row['event_uid'];
		$attender['created_at'] = $row['created_at'];
		$attender['status'] = $row['status'];
		
		return $attender;	

    }

    public function getDetailAttenderFromQuery($row, $db, $user_uid){
	
		$sql_user = $db->getUserProfile($row['user_uid'], $user_uid);
		$attender = $this->getUserFromQuery($sql_user, $db, $user_uid);
		$attender['attender_uid'] = $row['attender_uid'];
		$attender['created_at'] = $row['created_at'];
		$attender['status'] = $row['status'];	    
		
		return $attender;	

    }


    public function getAllAttendersFromQuery($attenders, $db, $user_uid){

		$response = array();
		$response['interested'] = array();
		$response['maybe'] = array();
		$response['going'] = array();

		return $response;

    }

    public function getPageFromQuery($sql_page, $db, $user_uid, $pdo = null){
        
		$page = array();
	    $page['page_uid'] = $sql_page['page_uid'];
	    $page['title'] = $sql_page['title'];
	    $page['description'] = $sql_page['description'];
	    $page['latitude'] = $sql_page['latitude'];
	    $page['longitude'] = $sql_page['longitude'];
	    $page['address'] = $sql_page['address'];
	    $page['created_at'] = $sql_page['created_at'];
		$page['profile_image'] = $sql_page['profile_image'];
		$page['profile_image_small'] = $sql_page['profile_image_small'];
		$page['banner_images'] = $sql_page['banner_images'];
		$page['post_count'] = $sql_page['post_count'];
		$page['follower_count'] = $sql_page['follower_count'];
		$page['is_following'] = $sql_page['followed'] > 0;
		$page['fame_count'] = $sql_page['fame_count'];
		$page['checkin_count'] = $sql_page['checkin_count'];
		$page['category'] = $sql_page['category'];
		$page['editable_by_others'] = $sql_page['editable_by_others'];
		$page['deleted'] = $sql_page['deleted'];
			
		$page['has_update_post'] = $db->isPageWithPostUpdateWithin24($page['page_uid'], $pdo);
		$page['has_update_event'] = $db->isPageWithEventUpdateWithin24($page['page_uid'], $pdo);
		$page['has_friend_post'] = $db->isPageWithFriendPostCheckinWithin24($page['page_uid'], $user_uid, $pdo);
		$page['has_friend_event'] = $db->isPageWithFriendEventCheckinWithin24($page['page_uid'], $user_uid, $pdo);
			
		$page['report_count'] = $db->getReportCount($page['page_uid'], 'page', $pdo);
	        		    
	    $sql_user = $db->getUserProfile($sql_page['user_uid'], $user_uid, $pdo);
	                          
	    $page['user'] = $this->getUserFromQuery($sql_user, $db, $user_uid, $pdo);
	        		 
	    $images = $db->getPostImages($page['page_uid'], 'page', $pdo);
	    $page['image'] = $images['image'];
	        
	        
	    return $page;

    }

    public function getUserFromQuery($sql_user, $db, $user_uid, $pdo = null){

		$user = array();
		$user['user_uid'] = $sql_user['user_uid'];
		$user['profile_image'] = $sql_user['profile_image'];
		$user['profile_image_small'] = $sql_user['profile_image_small'];
		$user['id'] = $sql_user['id'];
		$user['name'] = $sql_user['name'];
		$user['email'] = $sql_user['email'];
		$user['registered_at'] = $sql_user['registered_at'];
		$user['post_count'] = $sql_user['post_count'];

		$user['status'] = $db->getFriendStatus($user_uid, $user['user_uid'], $pdo);

		return $user;

    }

    public function getImageFromQuery($row){

    	$image = array();
		$image['image_uid'] = $row['image_uid'];
    	$image['path'] = $row['path'];
    	$image['description'] = $row['description'];
    	$image['type'] = $row['type'];
    	$image['width'] = $row['width'];
    	$image['height'] = $row['height'];
        	
    	return $image;
    }

    public function getRequestFromQuery($row, $db, $user_uid, $pdo = null){

	    $request = array();
        $request['request_uid'] = $row['request_uid'];
        $request['created_at'] = $row['created_at'];

	    $request['status'] = $row['status'];

	    if($request['from_user_uid'] == $user_uid){
			//User is the sender, so get receiver details
			$sql_user = $db->getUserProfile($request['to_user_uid'], $user_uid, $pdo);
			$request['user'] = $this->getUserFromQuery($sql_user, $db, $user_uid, $pdo);

	    }else{
			//User is the receiver, so get sender details
			$request['status'] = 'accepting';
			$sql_user = $db->getUserProfile($request['from_user_uid'], $user_uid, $pdo);
			$request['user'] = $this->getUserFromQuery($sql_user, $db, $user_uid, $pdo);
	    }
            
	    return $request;

    }

    public function getLikeFromQuery($row, $db, $user_uid, $pdo = null){
	
            $like = array();
            $like['like_uid'] = $row['like_uid'];
            $like['created_at'] = $row['created_at'];
            $like['post_uid'] = $row['post_uid'];
            
            $sql_user = $db->getUserProfile($row['user_uid'], $user_uid, $pdo);

            $like['user'] = $this->getUserFromQuery($sql_user, $db, $user_uid, $pdo);

		    return $like;

    }

    public function getCommentFromQuery($row, $db, $user_uid, $pdo = null){

	    	$comment = array();
            $comment['comment_uid'] = $row['comment_uid'];
            $comment['description'] = $row['description'];
            $comment['created_at'] = $row['created_at'];
            $comment['post_uid'] = $row['post_uid'];
            
            $sql_user = $db->getUserProfile($row['user_uid'], $user_uid, $pdo);
            
            $comment['user'] = $this->getUserFromQuery($sql_user, $db, $user_uid, $pdo);

		    return $comment;

    }

    public function getNotificationFromQuery($row, $db, $user_uid, $pdo = null){

    	$notification = array();
    	$notification['notification_uid'] = $row['notification_uid'];
    	$notification['created_at'] = $row['created_at'];
    	$notification['type'] = $row['type'];
    	$notification['content_type'] = $row['content_type'];
    	$notification['tag'] = $row['tag'];
    	$notification['error'] = "none";
    
    	if($row['content_type'] == 'user'){
    		$sql_user = $db->getUserProfile($row['content_user_uid'], $user_uid, $pdo);
    		$notification['user'] = $this->getUserFromQuery($sql_user, $db, $user_uid, $pdo);
    	}
    	else{
    		$sql_page = $db->getPageDetails($row['content_user_uid'], $user_uid, $pdo);
	        $notification['user'] = $this->getPageFromQuery($sql_page, $db, $user_uid, $pdo);
    	}
    	
    
    	if($row['tag'] == 'post'){
	    	$sql_post = $db->getPostDetails($row['post_uid'], $user_uid, $pdo);
		    $notification['post'] = $this->getPostFromQuery($sql_post, $db, $user_uid, $pdo);
	    } 
	    else if($row['tag'] == 'event'){
	       	$sql_event = $db->getEventDetails($row['post_uid'], $user_uid, $pdo);
	        $notification['event'] = $this->getEventFromQuery($sql_event, $db, $user_uid, $pdo);
	    }
	    else if($row['tag'] == 'page'){
	       	$sql_page = $db->getPageDetails($row['post_uid'], $user_uid, $pdo);
	        $notification['page'] = $this->getPageFromQuery($sql_page, $db, $user_uid, $pdo);
	    }
        
    	return $notification;

    }


}

?>
