<?php 
	if ($_FILES['file']['error'] > 0){
        echo json_encode(['code'=>1]);
    }else{
        if (!is_dir('upload/')){
            $res = mkdir(iconv('UTF-8','GBK','upload/'),0777,true); 
        }
        $date = time();
        $name = explode('.',$_FILES['file']['name']);
		move_uploaded_file($_FILES['file']['tmp_name'],'upload/'.$date.'.'.$name[1]);
        echo json_encode(['code'=>0,'data'=>'upload/'.$date.'.'.$name[1]]);
    }