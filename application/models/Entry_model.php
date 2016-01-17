<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once realpath(__DIR__ . '/../core/Basic_model.php');
class Entry_model extends Basic_model {
    
    public function manageNewEntryData($blogName, $entryTitle, $entry, $date) {
        if(!$this->checkIfBlogExist($blogName)) {
            return false;
        }
        
        $properBlogName = $this->getInternalBlogName($blogName);
        $seconds = date('s', time());
        $basicNewEntryFileName = $date . $seconds;
        $uniqunessNumber = $this->getNewEntryUniqueNumber($properBlogName, $basicNewEntryFileName);
        $newEntryFileFullName = $basicNewEntryFileName . $uniqunessNumber;
        
//        $sem = sem_get(3);
//        ob_flush();
//        flush();
//        sem_acquire($sem);
        
        $this->addEmptyFileFD($properBlogName, $newEntryFileFullName);
        $this->saveDataToFileFD($properBlogName, $newEntryFileFullName, array($entryTitle, $entry));
        $this->manageNewEntryUploadedFiles($blogName, $newEntryFileFullName);
        
//        ob_flush();
//        flush();
//        sem_release($sem);
    }
    
    protected function getNewEntryUniqueNumber($properBlogName, $basicNewEntryDirName) {
        $basicNewEntryDirNameRegExp = $this->fakeDatabaseDir . '/' . $properBlogName . '/' . $basicNewEntryDirName . '??';
        $blogSameTimeEntries = glob($basicNewEntryDirNameRegExp);
        $sameTimeEntriesCount = count($blogSameTimeEntries);

        if($sameTimeEntriesCount > 10) {
            $uniqunessNumber = $sameTimeEntriesCount;
        } else {
            $uniqunessNumber = '0' . $sameTimeEntriesCount;
        }
        
        return $uniqunessNumber;
    }
    
    public function getUserBlogProperName($userName, $password) {
        $blogsDirRough = glob($this->fakeDatabaseDir . '/*', GLOB_ONLYDIR);
        
        foreach($blogsDirRough as $blogDirRough) {
            $blogPathDirs = explode('/', $blogDirRough);
            $blogDir = $blogPathDirs[count($blogPathDirs) - 1];
            
            if($this->checkSingleBlogUserData($blogDir, $userName, $password)) {
                return $blogDir;
            }
        }
    }
    
    protected function checkSingleBlogUserData($properBlogName, $userName, $password) {
        if(!$this->checkIfFileExistsFD($properBlogName, 'info')) {
            return false;
        }
        
        $blogUserData = $this->readDataFromFileFD($properBlogName, 'info');
        $blogDataUserName = $blogUserData[0];
        $blogDataPasswordMD5 = $blogUserData[1];
        
        $passwordMD5 = md5($password);
        if($userName === $blogDataUserName && $passwordMD5 === $blogDataPasswordMD5) {
            return true;
        }
        return false;
    }
    
    protected function manageNewEntryUploadedFiles($blogName, $entryId) {
        $filesBasePath = $this->fakeDatabaseDir . '/' . $blogName . '/' . $entryId;
        
        $fileNumber = 0;
        foreach($_FILES as $file) {
            $uploadError = $file['error'];
            if($uploadError !== UPLOAD_ERR_OK) {
                error_log('Upload not successful!');
                continue;
            }
            
            $collidingFilesNamesRegExp = $filesBasePath . $fileNumber . '.*';
            $collidingFiles = glob($collidingFilesNamesRegExp);
            if ($collidingFiles && count($collidingFiles)) {
                error_log('Files collision!');
                continue;
            }
            
            error_log('adding file');
            $fileName = basename($file['name']);
            $fileExtension = strrchr($fileName, '.' );
            $newEntryFileFullPath = $filesBasePath . $fileNumber . $fileExtension;
            move_uploaded_file($file['tmp_name'], $newEntryFileFullPath);
            
            $fileNumber++;
        }
        
        return true;
    }
    
    protected function getUploadedFileInfo($fileName) {
        return isset($_FILES[$fileName]) ? $_FILES[$fileName] : null;
    }
    
//    Bad practice, code repetition!!!
    public function checkIfBlogExist($blogName) {
        $properBlogName = $this->getInternalBlogName($blogName);
        return $this->checkIfFileExistsFD('', $properBlogName);
    }
}