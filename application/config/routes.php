<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

// $route['test'] = 'test';
$route['online_count'] = 'online';
$route['login'] = 'user/login';
$route['register'] = 'user/register';
$route['logout'] = 'user/logout';

$route['settings'] = 'settings';
$route['settings/avatar'] = 'settings/avatar';
$route['settings/password'] = 'settings/password';
$route['settings/status_refuse'] = '/settings/status_refuse';
$route['settings/status_free'] = '/settings/status_free';


$route['members'] = 'member/get_all_members';
$route['member/(:any)'] = 'member/get_member/$1';
$route['random'] = 'member/random';
$route['rerandom'] = 'member/rerandom';

// ajax
$route['i/feed/create'] = 'feed/create';
$route['i/timeline'] = 'feed/timeline';

// footer
$route['about'] = 'about/index';
$route['faq'] = 'about/faq';
$route['why'] = 'about/why';
$route['contact'] = 'about/contact';
$route['feedback'] = 'about/feedback';
$route['special'] = 'about/special';

// admin
// $route['admin/feeds'] = 'admin_feed/get_all_feeds'; // 获取所有动态
// $route['admin/feeds/match/(:any)'] = 'admin_feed/get_match_feeds/$1'; // 获取某一个匹配的所有动态
// $route['admin/feeds/user/(:any)'] = 'admin_feed/get_user_feeds'; // 获取某一个用户的所有动态
// $route['admin/users'] = 'admin_user/index';
$route['admin/user/block/(:any)'] = 'admin_user/block/$1';

// $route['pages/view/(:any)'] = 'pages/view/$1';
// $route['(:any)'] = 'pages/view/$1';

// $route['default_controller'] = "welcome";
$route['default_controller'] = "home";
$route['404_override'] = 'error/index';


/* End of file routes.php */
/* Location: ./application/config/routes.php */