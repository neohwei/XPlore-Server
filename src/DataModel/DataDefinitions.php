<?php

define("TABLE_USER", "Users");
define("TABLE_POSTS", "Posts");
define("TABLE_COMMENTS", "Comments");
define("TABLE_LIKES", "Likes");
define("TABLE_PAGES", "Pages");
define("TABLE_REQUESTS", "Requests");
define("TABLE_IMAGES", "Images");
define("TABLE_NOTIFICATIONS", "Notifications");
define("TABLE_EVENTS", "Events");
define("TABLE_EVENT_ATTENDERS", "EventAttenders");
define("TABLE_FLAGS", "Flags");
define("TABLE_PAGE_CATEGORIES", "PageCategories");

/*
 * Variable for tags
 */

define("TAG_ERROR", "error");
define("TAG_SUCCESS", "success");
define("TAG_MESSAGE", "message");

define("TAG_POST", "post");
define("TAG_USER", "user");
define("TAG_COMMENT", "comment");
define("TAG_LIKE", "like");
define("TAG_PAGE","page");
define("TAG_IMAGE", "image");
define("TAG_FRIEND", "friend");
define("TAG_REQUEST", "request");
define("TAG_NOTIFICATION", "notification");
define("TAG_EVENT", "event");
define("TAG_EVENT_ATTENDER","event_attender");
define("TAG_FLAG", "flag");

/*
 * Variables for TABLE_USER
 */
define("USER_UID","user_uid");
define("USER_ID", "id");
define("USER_NAME", "name");
define("USER_EMAIL", "email");
define("USER_PASSWORD", "password"); //encrypted password with salt
define("USER_PROFILE_IMAGE", "profile_image");
define("USER_PROFILE_IMAGE_SMALL", "profile_image_small");
define("USER_CREATED_AT", "registered_at");
define("USER_UPDATED_AT", "updated_at");
define("USER_SALT", "salt"); //salt required to decrypt user password
define("USER_TOKEN", "token"); //token for authorization
define("USER_GCM_ID", "gcm_id");
define("USER_POST_COUNT", "post_count");
define("USER_FRIEND_COUNT", "friend_count");
define("USER_LAST_LATITUDE", "last_latitude");
define("USER_LAST_LONGITUDE", "last_longitude");
define("USER_LAST_LOCATION_DATETIME", "last_location_datetime");
define("USER_LAST_FAME_UPDATE", "last_fame_update");

//for profile update
define("USER_CURRENT_PASSWORD", "current_password");
define("USER_NEW_PASSWORD", "new_password");

/*
 * Variables for TABLE_PAGE
 */
define("PAGE_UID", "page_uid");
define("PAGE_CREATED_AT", "created_at");
define("PAGE_DESCRIPTION", "description");
define("PAGE_TITLE", "title");
define("PAGE_PROFILE_IMAGE", "profile_image");
define("PAGE_PROFILE_IMAGE_SMALL", "profile_image_small");
define("PAGE_BANNER_IMAGES", "banner_images");
define("PAGE_USER_UID",	"user_uid");
define("PAGE_LATITUDE", "latitude");
define("PAGE_LONGITUDE", "longitude");
define("PAGE_ADDRESS", "address");
define("PAGE_POST_COUNT", "post_count");
define("PAGE_FOLLOWER_COUNT", "follower_count");
define("PAGE_FAME_COUNT", "fame_count");
define("PAGE_CHECKIN_COUNT", "checkin_count");
define("PAGE_CATEGORY", "category");
define("PAGE_EDITABLE_BY_OTHERS", "editable_by_others");

define("PAGE_FAME_UP", "up");
define("PAGE_FAME_DOWN", "down");
define("PAGE_FAME_STATUS", "fame_status");
define("PAGE_IS_FOLLOWING", "is_following");


/*
 * Variables for TABLE_PAGE_CATEGORY
 */
define("PAGE_CATEGORY_UID", "category_uid");
define("PAGE_CATEGORY_NAME", "name");
define("PAGE_CATEGORY_CREATED_AT", "created_at");
define("PAGE_CATEGORY_USER_UID", "user_uid");

/*
 * Variables for TABLE_TRENDITEMS
 */
define("POST_UID", "post_uid");
define("POST_DESCRIPTION", "description");
define("POST_CREATED_AT", "created_at");
define("POST_LATITUDE", "latitude");
define("POST_LONGITUDE", "longitude");
define("POST_VISIBILITY", "visibility");
define("POST_ADDRESS", "address");
define("POST_LOCATION_TYPE", "location_type");
define("POST_TYPE", "type");
define("POST_LIKE_COUNT", "like_count");
define("POST_COMMENT_COUNT", "comment_count");
define("POST_USER_UID", "user_uid"); //link to user that posted the food
define("POST_IMAGE_COUNT", "image_count");
define("CHECKIN_PAGE_UID", "checkin_page_uid"); //check in to pages

/*
 * Variables for TABLE_EVENTS
 */
define("EVENT_UID", "event_uid");
define("EVENT_TITLE", "title");
define("EVENT_DESCRIPTION", "description");
define("EVENT_CREATED_AT", "created_at");
define("EVENT_START_DATE", "start_date");
define("EVENT_END_DATE", "end_date");
define("EVENT_START_TIME", "start_time");
define("EVENT_END_TIME", "end_time");
define("EVENT_LATITUDE", "latitude");
define("EVENT_LONGITUDE", "longitude");
define("EVENT_ADDRESS", "address");
define("EVENT_ORGANISER_UID", "organiser_uid");
define("EVENT_ORGANISER_TYPE", "organiser_type");
define("EVENT_LIKE_COUNT", "like_count");
define("EVENT_COMMENT_COUNT", "comment_count");
define("EVENT_IMAGE_COUNT", "image_count");
define("EVENT_VISIBILITY", "visibility");

/*
 * Variables for TABLE_EVENT_ATTENDERS
 */
define("ATTENDER_UID", "attender_uid");
define("ATTENDER_USER_UID", "user_uid");
define("ATTENDER_EVENT_UID", "event_uid");
define("ATTENDER_CREATED_AT", "created_at");
define("ATTENDER_STATUS", "status");
define("IS_USER_ATTENDED", "is_user_attended");

define("ATTENDER_STATUS_GOING", "going");
define("ATTENDER_STATUS_MAYBE", "maybe");
define("ATTENDER_STATUS_INTERESTED", "interested");
define("ATTENDER_STATUS_NOT_GOING", "not going");

/*
 * Variables for TABLE_IMAGES
 */
define("IMAGE_UID", "image_uid");
define("IMAGE_DESCRIPTION", "description");
define("IMAGE_POST_UID", "post_uid");
define("IMAGE_PATH", "path");
define("IMAGE_CREATED_AT", "created_at");
define("IMAGE_TYPE", "type");
define("HEIGHT", "height");
define("WIDTH", "width");

/*
 * Variables for TABLE_COMMENTS
 */
define("COMMENT_UID", "comment_uid");
define("COMMENT_DESCRIPTION", "description");
define("COMMENT_CREATED_AT", "created_at");
define("COMMENT_USER_UID", "user_uid");
define("COMMENT_POST_UID", "post_uid");


/*
 * Variables for TABLE_LIKES
 */
define("LIKE_UID", "like_uid");
define("LIKE_CREATED_AT", "created_at");
define("LIKE_USER_UID", "user_uid");
define("LIKE_POST_UID", "post_uid");
define("LIKED_BY_USER", "liked_by_user");


/*
   Variables for TABLE_FOLLOW_REQUEST
*/
define("REQUEST_UID", "request_uid");
define("REQUEST_CREATED_AT", "created_at");
define("REQUEST_FROM_USER_UID", "from_user_uid");
define("REQUEST_TO_USER_UID", "to_user_uid");
define("REQUEST_STATUS", "status");
define("REQUEST_TYPE", "type");

define("STATUS_PENDING", "pending");
define("STATUS_ACCEPTED", "accepted");
define("STATUS_NORMAL", "normal");

define("TYPE_USER", "user");
define("TYPE_PAGE", "page");


 /*
   Variables for TABLE_NOTIFICATION
 */
define("NOTIFICATION_UID", "notification_uid");
define("NOTIFICATION_CREATED_AT", "created_at");
define("NOTIFICATION_TYPE", "type");
define("NOTIFICATION_CONTENT_TYPE", "content_type");
define("NOTIFICATION_CONTENT_USER_UID", "content_user_uid");
define("NOTIFICATION_RECEIVING_USER_UID", "user_uid");
define("NOTIFICATION_POST_UID", "post_uid");

define("N_TYPE_LIKE", "like");
define("N_TYPE_COMMENT", "comment");
define("N_TYPE_FRIEND_REQUEST_RECEIVED", "request received");
define("N_TYPE_FRIEND_REQUEST_ACCEPTED", "request accepted");
define("N_TYPE_NEW_FOLLOWER", "new follower");
define("N_TYPE_NEARBY_POST", "nearby post");
define("N_TYPE_NEARBY_PAGE", "nearby page");
define("N_TYPE_NEARBY_EVENT", "nearby event");

define("N_CONTENT_TYPE_USER", "user");
define("N_CONTENT_TYPE_PAGE", "page");
define("N_CONTENT_TYPE_EVENT", "event");

define("NOTIFICATION_ERROR", "error");
define("N_ERROR_NONE", "none");

/*
 * Variables for TABLE_FLAGS
 */
define("FLAG_UID", "flag_uid");
define("FLAG_DESCRIPTION", "description");
define("FLAG_CREATED_AT", "created_at");
define("FLAG_LATITUDE", "latitude");
define("FLAG_LONGITUDE", "longitude");
define("FLAG_USER_UID", "user_uid");
define("FLAG_ADDRESS", "address");

/*
 * Variables for TABLE_REPORT
 */
define("REPORT_UID", "report_uid");
define("REPORT_POST_UID", "post_uid");
define("REPORT_USER_UID", "user_uid");
define("REPORT_CREATED_AT", "created_at");
define("REPORT_DESCRIPTION", "description");
define("REPORT_CATEGORY", "category");
define("REPORT_TAG", "tag");
define("REPORT_COUNT", "report_count");

/*
 * Variables for uploading files
 */
define("FILE_TOTAL", "file_number");
define("FILE_TAG", "file"); //append with number
define("FILE_NAME", "filename"); //append with number
define("FILE_TAG_PROFILE", "file_profile"); //tag for profile image file
define("FILE_NAME_PROFILE", "filename_profile"); //tag for profile image name

define("VIDEO_BITMAP_TAG", "video_bitmap");
define("VIDEO_BITMAP_NAME", "video_bitmap_name");

define("DELETE_FILE_TOTAL", "delete_file_number");

define("TYPE_IMAGE", "image");
define("TYPE_VIDEO", "video");

define("VISIBILITY_PUBLIC", "public");
define("VISIBILITY_PRIVATE", "private");

define("LOCATION_TYPE_SHOW", "show");
define("LOCATION_TYPE_HIDE", "hide");

define("NOTIFICATION_TYPE_NEARBY", "nearby");
define("NOTIFICATION_TYPE_FRIEND", "friend");
define("NOTIFICATION_TYPE_NONE", "none");

/*
 * Variables for others
 */
define("LATITUDE", "latitude");
define("LONGITUDE", "longitude");
define("KEYWORD", "keyword");
define("SEARCH_USER_UID", "search_user_uid");
define("IS_USER", "is_user");
define("CURSOR", "cursor");

define("CURSOR_CHECKIN", "cursor_checkin");
define("CURSOR_DATE", "cursor_date");

define("HAS_UPDATE_POST", "has_update_post");
define("HAS_UPDATE_EVENT", "has_update_event");
define("HAS_FRIEND_POST_CHECKIN", "has_friend_post");
define("HAS_FRIEND_EVENT_CHECKIN", "has_friend_event");

define("DELETED", "deleted");

define("TAG", "tag");
define("ID", "id");

?>
