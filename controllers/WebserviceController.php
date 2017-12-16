<?php

namespace app\controllers;

use app\models\Announcement;
use app\models\User;
use app\models\Attachment;
use app\models\Teacher;
use app\models\Student;
use app\models\Course;
use app\models\Follow;
use app\models\Time;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class WebserviceController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function beforeAction($action)
    {
        Yii::$app->controller->enableCsrfValidation = false;
        $request = Yii::$app->request;
        $csrfkey = \Yii::$app->params['CsrfApiKey'];
        $postedKey = $request->post('apiCsrfKey');
        if($postedKey == $csrfkey){
            return true;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return parent::beforeAction($action);
    }
    
    public function actionRegisteruser()
    {
        $model = new User();
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $username = $request->post('name');
        $password = Yii::$app->security->generatePasswordHash($request->post('password'));
        $email = $request->post('email');
        $role_id = $request->post('role_id');

        $model->user_password = $password;
        $model->user_email = $email;
        $model->user_role_id = $role_id;
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');
        
        /*if ($model->save()) 
        {
        	$response['data'] = $model->id;
        	return $response;
        }*/

        if ($model->save()) 
        {
           $response['data']['userID'] = $model->id;
           if ($role_id == 2)
        	{
        		$teacher_model = new Teacher();
        		
        		$profilePic = $request->post('profile_pic');
       			$imageName = Yii::$app->security->generateRandomString(10).'.png';
        		$imagePath = \Yii::$app->basePath . '/uploads/' . $imageName;
        		
        		if($profilePic){
            		$imageData = base64_decode($profilePic);
            		file_put_contents($imagePath, $imageData);
            		$teacher_model->profile_pic = $imagePath;
        		}
        		
        		$meta = $request->post('meta');
        		$qualification = $request->post('qualification');
        		$availablity = 'Available';
        		$status = 'Yes';
        		
        		$teacher_model->name = $username;
        		$teacher_model->user_id = $model->id;
        		$teacher_model->meta = $meta;
        		$teacher_model->qualification = $qualification;
        		$teacher_model->availablity = $availablity;
        		$teacher_model->status = $status;
        		
        		if ($teacher_model->save()) {
        			$response['data']['teacherID'] = $teacher_model->id;
        		}
        		else
        		{
        			$response['error_description'] = $teacher_model->getErrors();
        		}
        	}
        	else if ($role_id == 3)
        	{
        		$student_model = new Student();
        	
        		$profilePic = $request->post('profile_pic');
       			$imageName = Yii::$app->security->generateRandomString(10).'.png';
        		$imagePath = \Yii::$app->basePath . '/uploads/' . $imageName;
        		
        		if($profilePic){
            		$imageData = base64_decode($profilePic);
            		file_put_contents($imagePath, $imageData);
            		$student_model->profile_pic = $imagePath;
        		}
        		
        		$meta = $request->post('meta');
        		$ep_num = $request->post('ep_num');
        		$degree_program = $request->post('degree_program');
        		$enrolment_no = $request->post('enrolment_no');
        		$ep_nclassum = $request->post('class');
        		$section = $request->post('section');
        		$cgpa = $request->post('cgpa');
        		$year = $request->post('year');
        		$status = 'Yes';
        		
        		$student_model->name = $username;
        		$student_model->user_id = $model->id;
        		$student_model->meta = $meta;
        		$student_model->ep_num = $ep_num;
        		$student_model->degree_program = $degree_program;
        		$student_model->enrolment_no = $enrolment_no;
        		$student_model->status = $status;
        		$student_model->section = $section;
        		$student_model->cgpa = $cgpa;
        		$student_model->yaer = $year;
        		$student_model->enrolled_date = date('Y-m-d H:i:s');
        		
        		if ($student_model->save()) {
        			$response['data']['studentID'] = $student_model->id;
        		}
        		else
        		{
        			$response['error_description'] = $student_model->getErrors();
        		}
        	}
        }
        else{
        	$response['response'] = 'NOT SUCCESS';
        	$response['error'] = 'Something went wrong';
        	$response['error_description'] = $model->getErrors();	
        }
        return $response;
    }
    
    public function actionValidateuser()
    {
        $model = new User();
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $email = $request->post('email');
        $password = $request->post('password');
		$user =	User::findOne(['user_email' => $email,]);
        if(!empty($user)){
        	$message = $user->validatePassword($password);
        	if($message){
        		$user_role = $user->user_role_id;
        		if ($user_role == 2)
        		{
        			$teacher_model = Teacher::findOne(['user_id' => $user->id,]);
        			
        			if(!empty($teacher_model)){
        				$response['description']['TeacherData'] = $teacher_model;
        			}
        		}
        		else if ($user_role == 3) 
        		{
        			$student_model = Student::findOne(['user_id' => $user->id,]);
        			
        			if(!empty($student_model)){
        				$response['description']['StudentData'] = $student_model;
        			}
        		}
        		$response['data'] = $user;
       		 }
        	else{
        		$response['response'] = 'NOT SUCCESS';
        		$response['error'] = 'Incorrect password';
       		 }
        }
        else{
        	$response['response'] = 'NOT SUCCESS';
        	$response['error'] = 'Incorrect username';
       	}
        return $response;
    }
    
    public function actionResetpassword()
    {
        $model = new User();
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $userID = $request->post('userID');
        $oldPassword = $request->post('oldPassword');
        $newPassword = Yii::$app->security->generatePasswordHash($request->post('newPassword'));
        $user = User::findOne($userID);
        
        if(!empty($user)){
        	$validate = $user->validatePassword($oldPassword);
        	if($validate && $request->post('newPassword')){
        		$user->user_password = $newPassword;
        		if ($user->save()) {
           			$response['message'] = "Password successfully updated";
       		 	}
       		 }
        	else{
        		$response['response'] = 'NOT SUCCESS';
        		$response['error'] = 'Incorrect password';
        	}
        }
        
        else{
        		$response['response'] = 'NOT SUCCESS';
        		$response['error'] = 'Incorrect userid';
        	}
        return $response;
    }
    
    public function actionForgetpassword()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $email = $request->post('email');
        
        $user = User::findOne(['email' => $email,'status' => User::STATUS_ACTIVE,]);
        if(!empty($user)){
        	$newPassword = Yii::$app->security->generateRandomString(10);
        	$user->password_hash = Yii::$app->security->generatePasswordHash($newPassword);
        	if ($user->save()) {
           		$response['message'] = $newPassword;
           		Yii::$app->mailer->compose('request_email', ['user' => $user, 'password' => $newPassword])
					->setFrom([\Yii::$app->params['adminEmail'] => 'Fyp App'])
					->setTo($user->email)
					->setSubject("Rest Password")
					->send();
       		 }
        
        	else{
       	 		$response['response'] = 'NOT SUCCESS';
        		$response['error'] = 'Incorrect password';
       		 }
        }
        
        else{
       	 		$response['response'] = 'NOT SUCCESS';
        		$response['error'] = 'Email does not exist';
       		 }
        
        return $response;
    }
    
    public function actionEdituserprofilepic()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $userID = $request->post('userID');
        $role_id = $request->post('role_id');
        $profilePic = $request->post('profile_pic');

        $imageName = Yii::$app->security->generateRandomString(10).'.png';
        $imagePath = 'uploads/' . $imageName;

		if ($role_id == 2)
		{
			$teacher_model = Teacher::findOne(['user_id' => $user->id,]);
			
			if(!empty($teacher_model)){
        		if($teacher_model->profile_pic == "" || empty($teacher_model->profile_pic)){
                	$imagePath = 'uploads/' . $imageName;
            	}
            	else{
                	$imagePath = $teacher_model->profile_pic;
            	}
           		$imageData = base64_decode($profilePic);
            	file_put_contents($imagePath, $imageData);

            	$teacher_model->profile_pic = $imagePath;
        	}

        	if ($teacher_model->save()) {
            	$response['message'] = "Profile picture successfully updated";
        	}
        	else{
            	$response['response'] = 'NOT SUCCESS';
            	$response['error'] = 'Something went wrong';
        	}
		}
		else if ($role_id == 3)
		{
			$student_model = Student::findOne(['user_id' => $user->id,]);
			
			if(!empty($student_model)){
        		if($student_model->profile_pic == "" || empty($student_model->profile_pic)){
                	$imagePath = 'uploads/' . $imageName;
            	}
            	else{
                	$imagePath = $student_model->profile_pic;
            	}
           		$imageData = base64_decode($profilePic);
            	file_put_contents($imagePath, $imageData);

            	$student_model->profile_pic = $imagePath;
        	}

        	if ($student_model->save()) {
            	$response['message'] = "Profile picture successfully updated";
        	}
        	else{
            	$response['response'] = 'NOT SUCCESS';
            	$response['error'] = 'Something went wrong';
        	}
		}

        return $response;
    }
    
    public function actionGetallteachers()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        
        $course = Teacher::find()->where(['status' => "YES",])->asArray()->all();
        
        if(!empty($course)){
        	$response['data'] = $course;
        }
        
        else{
       	 	$response['message'] = 'No Teachers found!';
       	}
            
        return $response;
    }
    
    public function actionAddcourse()
    {
    	$model = new Course();
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $name = $request->post('name');
        $number = $request->post('number');
        $semester = $request->post('semester');
        $program = $request->post('program');
        $meta = $request->post('meta');
        $code = $request->post('code');
        $credit_hours = $request->post('credit_hours');
        $status = 'YES';

        if(!empty($name)){
            $model->name = $name;
            $model->number = $number;
            $model->semester = $semester;
            $model->program = $program;
            $model->status = $status;
            $model->code = $code;
            $model->credit_hours = $credit_hours;
            
            if(!empty($meta))
            {
            	$model->meta = $meta;
            }
        }

        if ($model->save()) {
            $response['message'] = "Course successfully added";
        }
        else{
            $response['response'] = 'NOT SUCCESS';
            $response['error'] = 'Something went wrong';
        }

        return $response;
    }
    
    public function actionGetallcourse()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        
        $course = $course = Course::find()->where(['status' => "YES",])->asArray()->all();
        
        if(!empty($course)){
        	$response['data'] = $course;
        }
        
        else{
       	 	$response['message'] = 'No Courses found!';
       	}
            
        return $response;
    }
    
    public function actionGetallcourseprogramwise()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $program = $request->post('program');
        
        $course = Course::find()->where(['program' => $program,])->asArray()->all();
        
        if(!empty($course)){
        	$response['data'] = $course;
        }
        
        else{
       	 	$response['message'] = 'No Courses found!';
       	}
            
        return $response;
    }
    
    public function actionAddfollow()
    {
    	$model = new Follow();
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $teacher_id = $request->post('teacher_id');
        $student_id = $request->post('student_id');
        $status = 'YES';

        if(!empty($teacher_id)){
            $model->teacher_id = $teacher_id;
            $model->student_id = $student_id;
            $model->status = $status;
        }

        if ($model->save()) {
            $response['message'] = "Follow successfully added";
        }
        else{
            $response['response'] = 'NOT SUCCESS';
            $response['error'] = 'Something went wrong';
        }

        return $response;
    }
    
    public function actionGetallfollowers()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $student_id = $request->post('student_id');
        
        $follow = Follow::find()->where(['student_id' => $student_id,'status' => "YES"])->asArray()->all();
        
        foreach ($follow as $key => $val){
            $follow[$key]['TeacherData'] = Teacher::find()->where(['id' => $val['teacher_id']])->asArray()->all();
        }
        
        if(!empty($follow)){
        	$response['data'] = $follow;
        }
        else{
       	 	$response['message'] = 'No Followers found!';
       	}
            
        return $response;
    }
    
    public function actionGetallfollows()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $teacher_id = $request->post('teacher_id');
        
        $follow = Follow::find()->where(['teacher_id' => $teacher_id,'status' => "YES"])->asArray()->all();
        
        foreach ($follow as $key => $val){
            $follow[$key]['StudentData'] = Student::find()->where(['id' => $val['student_id']])->asArray()->all();
        }
        
        if(!empty($follow)){
        	$response['data'] = $follow;
        }
        else{
       	 	$response['message'] = 'No Follows found!';
       	}
            
        return $response;
    }

    public function actionAddtime()
    {
    	$model = new Time();
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $teacher_id = $request->post('teacher_id');
        $course_id = $request->post('course_id');
        $day = $request->post('day');
        $program = $request->post('program');
        $meta = $request->post('meta');
        $year = $request->post('year');
        $date = date('Y-m-d H:i:s');
        $morning_evening = $request->post('is_morning');
        $time_duration = $request->post('time_duration');
        $semester = $request->post('semester');
        $is_theory = $request->post('is_theory');
        $section = $request->post('section');
        $status = 'YES';

        if(!empty($teacher_id)){
            $model->teacher_id = $teacher_id;
            $model->course_id = $course_id;
            $model->day = $day;
            $model->program = $program;
            $model->status = $status;
            $model->date = $date;
            $model->year = $year;
            $model->is_morning = $morning_evening;
            $model->time_duration = $time_duration;
            $model->semester = $semester;
            $model->is_theory = $is_theory;
            $model->section = $section;
            if(!empty($meta))
            {
            	$model->meta = $meta;
            }
        }

        if ($model->save()) {
            $response['data'] = $model->id;
            $response['message'] = "Time successfully added";
        }
        else{
            $response['response'] = 'NOT SUCCESS';
            $response['error'] = 'Something went wrong';
        }

        return $response;
    }
    
    public function actionGettimecoursewise()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $course_id = $request->post('course_id');
        
        $time = Time::find()->where(['course_id' => $course_id,'status' => "YES"])->asArray()->all();
        
        foreach ($time as $key => $val){
            $time[$key]['CourseData'] = Course::find()->where(['id' => $val['course_id']])->asArray()->all();
            $time[$key]['TeacherData'] = Teacher::find()->where(['id' => $val['teacher_id']])->asArray()->all();
        }
        
        if(!empty($time)){
        	$response['data'] = $time;
        }
        else{
       	 	$response['message'] = 'No Commitments found!';
       	}
            
        return $response;
    }
    
    public function actionGettimeteacherwise()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $teacher_id = $request->post('teacher_id');
        
        $time = Time::find()->where(['teacher_id' => $teacher_id,'status' => "YES"])->asArray()->all();
        
        foreach ($time as $key => $val){
            $time[$key]['CourseData'] = Course::find()->where(['id' => $val['course_id']])->asArray()->all();
            $time[$key]['TeacherData'] = Teacher::find()->where(['id' => $val['teacher_id']])->asArray()->all();
        }
        
        if(!empty($time)){
        	$response['data'] = $time;
        }
        else{
       	 	$response['message'] = 'No Commitments found!';
       	}
            
        return $response;
    }
    
    public function actionGettimemorningeveningwise()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $is_morning = $request->post('is_morning');
        
        $time = Time::find()->where(['is_morning' => $is_morning,'status' => "YES"])->asArray()->all();
        
        foreach ($time as $key => $val){
            $time[$key]['CourseData'] = Course::find()->where(['id' => $val['course_id']])->asArray()->all();
            $time[$key]['TeacherData'] = Teacher::find()->where(['id' => $val['teacher_id']])->asArray()->all();
        }
        
        if(!empty($time)){
        	$response['data'] = $time;
        }
        else{
       	 	$response['message'] = 'No Commitments found!';
       	}
            
        return $response;
    }
    
    public function actionGettimetable()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $is_morning = $request->post('is_morning');
        $program = $request->post('program');
        $semester = $request->post('semester');
        $year = $request->post('year');
        
        $time = Time::find()->where(['is_morning' => $is_morning,'program' => $program,'semester' => $semester,'year' => $year, 'status' => "YES"])->asArray()->all();
        
        foreach ($time as $key => $val){
            $time[$key]['CourseData'] = Course::find()->where(['id' => $val['course_id']])->asArray()->all();
            $time[$key]['TeacherData'] = Teacher::find()->where(['id' => $val['teacher_id']])->asArray()->all();
        }
        
        if(!empty($time)){
        	$response['data'] = $time;
        }
        else{
       	 	$response['message'] = 'No Commitments found!';
       	}
            
        return $response;
    }
    
    public function actionAddannouncement()
    {
    	$model = new Announcement();
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $teacher_id = $request->post('teacher_id');
        $description = $request->post('description');
        $meta = $request->post('meta');
        $status = 'YES';

        if(!empty($teacher_id)){
            $model->teacher_id = $teacher_id;
            $model->description = $description;
            $model->status = $status;
            if(!empty($meta))
            {
            	$model->meta = $meta;
            }
        }

        if ($model->save()) {
            $response['message'] = "Announcement successfully added";
        }
        else{
            $response['response'] = 'NOT SUCCESS';
            $response['error'] = 'Something went wrong';
        }

        return $response;
    }
    
    public function actionGetallannouncement()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        
        $announcement = Announcement::find()->asArray()->all();//->all();
        
        foreach ($announcement as $key => $val){
            $announcement[$key]['TeacherData'] = Teacher::find()->where(['id' => $val['teacher_id']])->asArray()->all();
        }
        
        if(!empty($announcement)){
        	$response['data'] = $announcement;
        }
        else{
       	 	$response['message'] = 'No Announcement found!';
       	}
            
        return $response;
    }
    
    public function actionAddattachment()
    {
    	$model = new Attachment();
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $name = $request->post('name');
        $type = $request->post('type');
        $description = $request->post('description');
        $teacher_id = $request->post('teacher_id');
        $is_result = $request->post('is_result');
        $meta = $request->post('meta');
        $title = $request->post('title');
        $status = 'YES';

        if(!empty($teacher_id)){
            $model->teacher_id = $teacher_id;
            $model->description = $description;
            $model->status = $status;
            $model->name = $name;
            $model->type = $type;
            $model->is_result = $is_result;
            $model->title = $title;
            $model->created_at = date('Y-m-d H:i:s');
        	$model->updated_at = date('Y-m-d H:i:s');
            
//             $profilePic = $request->post('profile_pic');
       		// $imageName = Yii::$app->security->generateRandomString(10).'.png';
//         	$imagePath = 'uploads/' . $imageName;
//         		
//         	if($name){
//             	$imageData = base64_decode($name);
//             	file_put_contents($imagePath, $imageData);
//             	$model->name = $imagePath;
//         	}
            
            if(!empty($meta))
            {
            	$model->meta = $meta;
            }
        }

        if ($model->save()) {
            $response['message'] = "Attachment successfully added";
        }
        else{
            $response['response'] = 'NOT SUCCESS';
            $response['error'] = 'Something went wrong';
        }
        return $response;
    }
    
    public function actionGetteacherattachment()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $teacher_id = $request->post('teacher_id');
        
        $attachment = Attachment::find()->where(['teacher_id' => $teacher_id,'status' => "YES"])->asArray()->all();
        
        foreach ($attachment as $key => $val){
            $attachment[$key]['TeacherData'] = Teacher::find()->where(['id' => $val['teacher_id']])->asArray()->all();
        }
        
        if(!empty($attachment)){
        	$response['data'] = $attachment;
        }
        else{
       	 	$response['message'] = 'No Announcement found!';
       	}
            
        return $response;
    }
    
    public function actionGetattachment()
    {
        $response = array();
        $response['response'] = 'SUCCESS';
        $response['error'] = 'NULL';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $student_id = $request->post('student_id');
        
        $query = new Query();
        $query	->select([
                'follow.*',
                'attachment.*']
        )
            ->from('follow')
            ->andFilterWhere(['student_id' => $student_id])
            ->join('LEFT OUTER JOIN', 'attachment',
                'attachment.teacher_id = follow.teacher_id');

        $command = $query->createCommand();
        $attachment = $command->queryAll();
        
        foreach ($attachment as $key => $val){
            $attachment[$key]['TeacherData'] = Teacher::find()->where(['id' => $val['teacher_id']])->asArray()->all();
        }
        
        if(!empty($attachment)){
        	$response['data'] = $attachment;
        }
        else{
       	 	$response['message'] = 'No Announcement found!';
       	}
            
        return $response;
    }

}
