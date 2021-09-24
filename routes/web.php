<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// require("api.php");

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\FirebaseServices;
use Google\Cloud\Firestore\FieldValue;
use Google\Cloud\Firestore\FieldPath;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\Transaction;

use App\Helpers;
use App\Services\GroupUserService;
use App\Services\PostService;
use App\Services\UserService;
use Illuminate\Support\Facades\Session;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\MessageBuilder\Text\EmojiBuilder;
use LINE\LINEBot\MessageBuilder\Text\EmojiTextBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

// Route::group(['middleware' => ['get.menu']], function () {
//     Route::get('/', function () {           return view('dashboard.homepage'); });

//     Route::group(['middleware' => ['role:user']], function () {
//         Route::get('/colors', function () {     return view('dashboard.colors'); });
//         Route::get('/typography', function () { return view('dashboard.typography'); });
//         Route::get('/charts', function () {     return view('dashboard.charts'); });
//         Route::get('/widgets', function () {    return view('dashboard.widgets'); });
//         Route::get('/404', function () {        return view('dashboard.404'); });
//         Route::get('/500', function () {        return view('dashboard.500'); });
//         Route::prefix('base')->group(function () {  
//             Route::get('/breadcrumb', function(){   return view('dashboard.base.breadcrumb'); });
//             Route::get('/cards', function(){        return view('dashboard.base.cards'); });
//             Route::get('/carousel', function(){     return view('dashboard.base.carousel'); });
//             Route::get('/collapse', function(){     return view('dashboard.base.collapse'); });

//             Route::get('/forms', function(){        return view('dashboard.base.forms'); });
//             Route::get('/jumbotron', function(){    return view('dashboard.base.jumbotron'); });
//             Route::get('/list-group', function(){   return view('dashboard.base.list-group'); });
//             Route::get('/navs', function(){         return view('dashboard.base.navs'); });

//             Route::get('/pagination', function(){   return view('dashboard.base.pagination'); });
//             Route::get('/popovers', function(){     return view('dashboard.base.popovers'); });
//             Route::get('/progress', function(){     return view('dashboard.base.progress'); });
//             Route::get('/scrollspy', function(){    return view('dashboard.base.scrollspy'); });

//             Route::get('/switches', function(){     return view('dashboard.base.switches'); });
//             Route::get('/tables', function () {     return view('dashboard.base.tables'); });
//             Route::get('/tabs', function () {       return view('dashboard.base.tabs'); });
//             Route::get('/tooltips', function () {   return view('dashboard.base.tooltips'); });
//         });
//         Route::prefix('buttons')->group(function () {  
//             Route::get('/buttons', function(){          return view('dashboard.buttons.buttons'); });
//             Route::get('/button-group', function(){     return view('dashboard.buttons.button-group'); });
//             Route::get('/dropdowns', function(){        return view('dashboard.buttons.dropdowns'); });
//             Route::get('/brand-buttons', function(){    return view('dashboard.buttons.brand-buttons'); });
//         });
//         Route::prefix('icon')->group(function () {  // word: "icons" - not working as part of adress
//             Route::get('/coreui-icons', function(){         return view('dashboard.icons.coreui-icons'); });
//             Route::get('/flags', function(){                return view('dashboard.icons.flags'); });
//             Route::get('/brands', function(){               return view('dashboard.icons.brands'); });
//         });
//         Route::prefix('notifications')->group(function () {  
//             Route::get('/alerts', function(){   return view('dashboard.notifications.alerts'); });
//             Route::get('/badge', function(){    return view('dashboard.notifications.badge'); });
//             Route::get('/modals', function(){   return view('dashboard.notifications.modals'); });
//         });
//         Route::resource('notes', 'NotesController');
//     });
//     Auth::routes();

//     Route::resource('resource/{table}/resource', 'ResourceController')->names([
//         'index'     => 'resource.index',
//         'create'    => 'resource.create',
//         'store'     => 'resource.store',
//         'show'      => 'resource.show',
//         'edit'      => 'resource.edit',
//         'update'    => 'resource.update',
//         'destroy'   => 'resource.destroy'
//     ]);

//     Route::group(['middleware' => ['role:user']], function () {
        
//         Route::prefix('manage-news')->group(function() {
//             Route::prefix('category')->group(function () {
//                 // Route::get('', 'CategoryController@index')->name('category.index');
//                 // Route::get('/{id}', 'CategoryController@getById')->name('category.getById');
//                 // Route::post('', 'CategoryController@create')->name('category.create');
//                 // Route::put('', 'CategoryController@update')->name('category.update');
//                 // Route::delete('', 'CategoryController@delete')->name('category.delete');
                
//                 // Firebase database functionality
//                 Route::get('', 'CategoryController@fbIndex')->name('category.index');
//                 Route::get('/{id}', 'CategoryController@fbGetById')->name('category.getById');
//                 Route::post('', 'CategoryController@fbCreate')->name('category.create');
//                 Route::put('', 'CategoryController@fbUpdate')->name('category.update');
//                 Route::delete('', 'CategoryController@fbDelete')->name('category.delete');
//             });
//             Route::prefix('post')->group(function () {
//                 Route::get('', 'PostController@index')->name('post.index');
//                 Route::get('/{id}', 'PostController@getById')->name('post.getById');
//                 Route::post('', 'PostController@create')->name('post.create');
//                 Route::put('', 'PostController@update')->name('post.update');
//                 Route::delete('', 'PostController@delete')->name('post.delete');
//             });
//         });
//         Route::resource('bread',  'BreadController');   //create BREAD (resource)
//         Route::resource('users',        'UsersController')->except( ['create', 'store'] );
//         Route::resource('roles',        'RolesController');
//         Route::resource('mail',        'MailController');
//         Route::get('prepareSend/{id}',        'MailController@prepareSend')->name('prepareSend');
//         Route::post('mailSend/{id}',        'MailController@send')->name('mailSend');
//         Route::get('/roles/move/move-up',      'RolesController@moveUp')->name('roles.up');
//         Route::get('/roles/move/move-down',    'RolesController@moveDown')->name('roles.down');
//         Route::prefix('menu/element')->group(function () { 
//             Route::get('/',             'MenuElementController@index')->name('menu.index');
//             Route::get('/move-up',      'MenuElementController@moveUp')->name('menu.up');
//             Route::get('/move-down',    'MenuElementController@moveDown')->name('menu.down');
//             Route::get('/create',       'MenuElementController@create')->name('menu.create');
//             Route::post('/store',       'MenuElementController@store')->name('menu.store');
//             Route::get('/get-parents',  'MenuElementController@getParents');
//             Route::get('/edit',         'MenuElementController@edit')->name('menu.edit');
//             Route::post('/update',      'MenuElementController@update')->name('menu.update');
//             Route::get('/show',         'MenuElementController@show')->name('menu.show');
//             Route::get('/delete',       'MenuElementController@delete')->name('menu.delete');
//         });
//         Route::prefix('menu/menu')->group(function () { 
//             Route::get('/',         'MenuController@index')->name('menu.menu.index');
//             Route::get('/create',   'MenuController@create')->name('menu.menu.create');
//             Route::post('/store',   'MenuController@store')->name('menu.menu.store');
//             Route::get('/edit',     'MenuController@edit')->name('menu.menu.edit');
//             Route::post('/update',  'MenuController@update')->name('menu.menu.update');
//             Route::get('/delete',   'MenuController@delete')->name('menu.menu.delete');
//         });
//         Route::prefix('media')->group(function () {
//             Route::get('/',                 'MediaController@index')->name('media.folder.index');
//             Route::get('/folder/store',     'MediaController@folderAdd')->name('media.folder.add');
//             Route::post('/folder/update',   'MediaController@folderUpdate')->name('media.folder.update');
//             Route::get('/folder',           'MediaController@folder')->name('media.folder');
//             Route::post('/folder/move',     'MediaController@folderMove')->name('media.folder.move');
//             Route::post('/folder/delete',   'MediaController@folderDelete')->name('media.folder.delete');;

//             Route::post('/file/store',      'MediaController@fileAdd')->name('media.file.add');
//             Route::get('/file',             'MediaController@file');
//             Route::post('/file/delete',     'MediaController@fileDelete')->name('media.file.delete');
//             Route::post('/file/update',     'MediaController@fileUpdate')->name('media.file.update');
//             Route::post('/file/move',       'MediaController@fileMove')->name('media.file.move');
//             Route::post('/file/cropp',      'MediaController@cropp');
//             Route::get('/file/copy',        'MediaController@fileCopy')->name('media.file.copy');
//         });
//     });
// });

Route:: get('readTextImage', function(){
    return view("test");
});

Route::get('', function(){
    echo "welcome!";
});

Route::put('/test', function(Request $request){
    $dataGroupUser = $request->input("groupUser");

    $user = UserService::userCollection()->document("xkuVCDQ4WXNnBOnS5QD6A4OdXAD3");
    $groupUserDocuments = UserService::groupUserCollection()->documents();
                
    UserService::transaction(function($transaction) use($user, $dataGroupUser, $groupUserDocuments){
                    foreach($groupUserDocuments as $groupDocSnapshot)
                    {
                        in_array($groupDocSnapshot->id(), $dataGroupUser) 
                        && (
                            $transaction->update($groupDocSnapshot->reference(), [
                                [
                                    "path" => "users",
                                    "value" => FieldValue::arrayUnion([$user])
                                ]
                            ]) 
                            || 1
                        ) 
                        || $transaction->update($groupDocSnapshot->reference(), [
                            [
                                "path" => "users",
                                "value" => FieldValue::arrayRemove([$user])
                            ]
                        ]);
                    }
                });
    echo "<pre>";
    // var_dump($data);
    echo "</prev>";
});

Route::post('/test', function(Request $request){

});

function a($c, $b = 1)
{
    $args = func_get_args();
    $atract = array_slice($args, 1);
    return $atract;
}
Route::get('/test', function(Request $request){
    $buildEmoji = new EmojiTextBuilder("$ LINE emoji $", new EmojiBuilder(0, "5ac1bfd5040ab15980c9b435", "001"),
    new EmojiBuilder(13, "5ac1bfd5040ab15980c9b435", "002"));

    // $arr = [
    //     new EmojiBuilder(0, "5ac1bfd5040ab15980c9b435", "001"),
    //     new EmojiBuilder(13, "5ac1bfd5040ab15980c9b435", "002")
    // ];
    // $new = array_map(function ($emoji) {
    //     return $emoji->build();
    // }, $arr);

    // $result = $buildEmoji->build();
    // $onfleetSignature = $_SERVER['X-Onfleet-Signature'];    
    // $data = UserService::getUserById("0dOlCCgTEfViKMF8QFMZZkNeQ4H3");
    // $ip = $request->ip();
    // $details = json_decode(file_get_contents("http://ipinfo.io/125.234.76.254/json"));

    // $user = UserService::userCollection()->document("5OYS8wC1oQZl7fAmcHp6taNHmuv1");
    // $adminGroupDoc = GroupUserService::getAdminGroup();
    // $usersInGroup = $adminGroupDoc->snapshot()->exists() ? $adminGroupDoc->snapshot()->data()["users"] : [];
    
    // array_push($usersInGroup, $user);

    // UserService::transaction(function($transaction) use($user, $adminGroupDoc) {
    //     $transaction->update($adminGroupDoc, [
    //         [
    //             "path" => "users",
    //             "value" => FieldValue::arrayUnion([$user])
    //         ]
    //     ]);
    // });
    $builTextMessage = new TextMessageBuilder($buildEmoji);

    echo "<pre>";
    var_dump($builTextMessage);

    // var_dump(true);
    // var_dump(GroupUserService::getGroupUser());
    // foreach($doc as $d){
        // var_dump(FieldValue::arrayUnion([$user]));
        echo "<br>";
    // }
// GroupUserService::fbIndex();
// var_dump(UserService::getUserById("XGzw9uFZ9pU3Ip5aXaXjEUkhW4A2"));
    echo "</prev>";
    // echo Helpers::CONVERT_vn($text);
    // $database = FirebaseServices::database();
    // $col = $database->collection("category");
    // $doc1 = $col->document("a");
    // $doc2 = $col->document("b");
    // $doc = (object)$doc;
    // echo $doc->createdAt->get()->format('d-m-Y');
    // $refDoc = $doc->parent_id;
    // $firestore = new FirestoreClient();
    // $fieldPath = $firestore->fieldPath(['category', 'PzjRjoJ1DT7sMOLtb8h4']);
    // $fieldPath = FieldPath::fromString("category.PzjRjoJ1DT7sMOLtb8h4");

    // $doc->update([
    //     ['path' => 'parent_id', 'value' => $col->document("PzjRjoJ1DT7sMOLtb8h4")]
    // ]);

    // FirebaseServices::transaction(function($transaction) use($doc1, $doc2){
    //     $transaction->create($doc1, [
    //         "newfield" => "field"
    //     ]);
    //     $transaction->create($doc2, [
    //         "newfield" => "field"
    //     ]);
    // });

    // echo "<pre>";
    // var_dump($fieldPath);
    // echo "</pre>";
    // $table = "example";
    // $collection = $database->collection($table);
    // $dataTable = DB::table($table)->get();
    // foreach($dataTable as $data)
    // {
    //     $_data = [];
    //     foreach($data as $key => $value)
    //     {
    //         switch($key)
    //         {
    //             case 'created_at':
    //                 $_data['createdAt'] = FieldValue::serverTimestamp();
    //                 break;
    //             case 'updated_at':
    //                 $_data['updatedAt'] = FieldValue::serverTimestamp();
    //                 break;
    //             case 'deleted_at':
    //                 $_data['deletedAt'] = NULL;
    //                 break;
    //             default:
    //                 $_data[$key] = $value;
    //                 break;
    //         }
    //     }
    //     $collection->add($_data);
    // }

})->name('test');

Route::get('exception', 'ExceptionController@index')->name('hanlerException');

Route::get('signup', 'UserController@signupPage')->name('signupPage');

Route::post('signup', 'UserController@signup')->name('signup');

Route::get('signin', 'UserController@signinPage')->name('signinPage')->middleware('guest');

Route::post('signin', 'UserController@signin')->name('signin')->middleware('guest');

Route::group(['middleware' => 'AuthMiddleware'], function(){

    Route::get('', 'HomeController@index')->name('home');

    Route::prefix('group-users')->group(function(){

        Route::get('', 'GroupUserController@fbIndex')->name('group.index');

        Route::get('/{id}', 'GroupUserController@fbGetById')->name('group.getById');

        Route::post('', 'GroupUserController@fbCreate')->name('group.create');

        Route::put('', 'GroupUserController@fbUpdate')->name('group.update');

        Route::delete('', 'GroupUserController@fbDelete')->name('group.delete');
    });

    Route::prefix('user')->group(function(){

        Route::get('', 'UserController@index')->name('user.index');

        Route::get('/{id}', 'UserController@edit')->name('user.edit');

        Route::get('reset_password', 'UserController@resetPasswordPage')->name('resetPasswordPage');
    
        Route::get('prompt_email_reset_password', 'UserController@emailResetPasswordPage')->name('emailResetPasswordPage')->middleware('guest')->withoutMiddleware('AuthMiddleware');

        Route::post('', 'UserController@create')->name('user.create');

        Route::put('reset_password', 'UserController@resetPassword')->name('resetPassword');
    
        Route::put('prompt_email_reset_password', 'UserController@emailResetPassword')->name('emailResetPassword');
    
        Route::put('signout', 'UserController@signout')->name('signout');

        Route::put('', 'UserController@update')->name('user.update');

        Route::delete('', 'UserController@delete')->name('user.delete');

    });

    Route::prefix('category')->group(function(){

        Route::get('', 'CategoryController@fbIndex')->name('category.index');

        Route::get('/{id}', 'CategoryController@fbGetById')->name('category.getById');

        Route::post('', 'CategoryController@fbCreate')->name('category.create');

        Route::put('', 'CategoryController@fbUpdate')->name('category.update');

        Route::delete('', 'CategoryController@fbDelete')->name('category.delete');

    });

    Route::prefix('post')->group(function(){

        Route::get('', 'PostController@fbIndex')->name('post.index');

        Route::get('/{id}', 'PostController@fbGetById')->name('post.getById');

        Route::post('', 'PostController@fbCreate')->name('post.create');

        Route::put('', 'PostController@fbUpdate')->name('post.update');

        Route::delete('', 'PostController@fbDelete')->name('post.delete');
    });
});

//Upload file
Route::post('uploadCKeditor', 'UploadCotroller@uploadCKeditor')->middleware('AuthMiddleware')->name('uploadCKeditor');