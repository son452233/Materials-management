<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class GoogleDriveController extends Controller
{
    public $gClient;

    function __construct()
    {

        $this->gClient = new \Google_Client();

        $this->gClient->setApplicationName('Web client 1'); // ADD YOUR AUTH2 APPLICATION NAME (WHEN YOUR GENERATE SECRATE KEY)
        $this->gClient->setClientId('748879742642-bv9j8i6t7vg5g3sbt70sqd7dnrt56d27.apps.googleusercontent.com');
        $this->gClient->setClientSecret('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');
        $this->gClient->setRedirectUri(route('google.login'));
        $this->gClient->setDeveloperKey('AIzaSyAF9nEl8ZmrcjgdFqT3y_IIvqINKSGBi5Y');
        $this->gClient->setScopes(array(
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/drive'
        ));

        $this->gClient->setAccessType("offline");

        $this->gClient->setApprovalPrompt("force");
    }

    public function googleLogin(Request $request)
    {

        $google_oauthV2 = new \Google_Service_Oauth2($this->gClient);

        if ($request->get('code')) {

            $this->gClient->authenticate($request->get('code'));

            $request->session()->put('token', $this->gClient->getAccessToken());
        }

        if ($request->session()->get('token')) {

            $this->gClient->setAccessToken($request->session()->get('token'));
        }

        if ($this->gClient->getAccessToken()) {

            //FOR LOGGED IN USER, GET DETAILS FROM GOOGLE USING ACCES
            $user = User::find(1);

            $user->access_token = json_encode($request->session()->get('token'));

            $user->save();

            dd("Successfully authenticated");
        } else {

            // FOR GUEST USER, GET GOOGLE LOGIN URL
            $authUrl = $this->gClient->createAuthUrl();

            return redirect()->to($authUrl);
        }
    }

    public function googleDriveFilePpload()
    {
        $service = new \Google_Service_Drive($this->gClient);

        $user = User::find(1);

        $this->gClient->setAccessToken(json_decode($user->access_token, true));

        if ($this->gClient->isAccessTokenExpired()) {

            // SAVE REFRESH TOKEN TO SOME VARIABLE
            $refreshTokenSaved = $this->gClient->getRefreshToken();

            // UPDATE ACCESS TOKEN
            $this->gClient->fetchAccessTokenWithRefreshToken($refreshTokenSaved);

            // PASS ACCESS TOKEN TO SOME VARIABLE
            $updatedAccessToken = $this->gClient->getAccessToken();

            // APPEND REFRESH TOKEN
            $updatedAccessToken['refresh_token'] = $refreshTokenSaved;

            // SET THE NEW ACCES TOKEN
            $this->gClient->setAccessToken($updatedAccessToken);

            $user->access_token = $updatedAccessToken;

            $user->save();
        }

        $fileMetadata = new \Google_Service_Drive_DriveFile(array(
            'name' => 'quanlyvattu',             // ADD YOUR GOOGLE DRIVE FOLDER NAME
            'mimeType' => 'application/vnd.google-apps.folder'
        ));

        $folder = $service->files->create($fileMetadata, array('fields' => 'id'));

        printf("Folder ID: %s\n", $folder->id);

        $file = new \Google_Service_Drive_DriveFile(array('name' => rand(11111, 99999) . 'jpg', 'parents' => array($folder->id)));

        $result = $service->files->create($file, array(

            'data' => file_get_contents(public_path('Screenshot_1.png')), // ADD YOUR FILE PATH WHICH YOU WANT TO UPLOAD ON GOOGLE DRIVE
            'mimeType' => 'application/octet-stream',
            'uploadType' => 'media'
        ));

        // GET URL OF UPLOADED FILE

        $url = 'https://drive.google.com/open?id=' . $result->id;

        dd($result);
    }
    // public function googleDriveFilePpload()
    // {
    //     $service = new \Google_Service_Drive($this->gClient);

    //     $user = User::find(1);

    //     $this->gClient->setAccessToken(json_decode($user->access_token, true));

    //     if ($this->gClient->isAccessTokenExpired()) {
    //         // Xử lý làm mới access token nếu cần thiết
    //     }

    //     $folderId = 'ID_của_thư_mục_tải_lên'; // ID của thư mục Google Drive bạn muốn tải lên

    //     $fileMetadata = new \Google_Service_Drive_DriveFile([
    //         'name' => 'Tên_tệp_moi.jpg', // Thay tên tệp theo ý bạn
    //         'parents' => [$folderId],
    //     ]);

    //     $fileContent = file_get_contents(storage_path('app/public/storage/images/i8.jpg'));

    //     $result = $service->files->create($fileMetadata, [
    //         'data' => $fileContent,
    //         'mimeType' => 'image/jpeg', // Thay đổi kiểu MIME tùy theo loại tệp
    //         'uploadType' => 'media',
    //     ]);

    //     // Lấy URL của tệp đã tải lên
    //     $url = 'https://drive.google.com/open?id=' . $result->id;

    //     dd($result);
    // }
}
