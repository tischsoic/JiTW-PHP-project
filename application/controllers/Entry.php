<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once '/../core/Basic_controller.php';
class Entry extends Basic_controller {
    
    public function __construct() {
        parent::__construct();
        $this->loadModel('Entry');
    }
    
//    public function add_entry() {
//        $blogName = $this->getPostData('blog_name');
//        $entryId = $this->getPostData('entry_id');
//        $commentType = $this->getPostData('type');
//        $nickname = $this->getPostData('nickname');
//        $content = $this->getPostData('content');
//
//        if($commentType && $nickname && $content && $blogName && $entryId) {
//            $this->model->manageNewCommentData($commentType, $nickname, $content, $blogName, $entryId);
//            $this->loadView('new_comment');
//        }
//        
//        $this->loadView('new_comment');
//    }

}