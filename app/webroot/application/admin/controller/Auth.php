<?php
/**
 * Email:zhaojunlike@gmail.com
 * Date: 7/8/2017
 * Time: 5:28 PM
 */

namespace app\admin\controller;


use app\common\cache\AuthCache;
use think\Session;
use app\common\core\Auth as ViewAuth;

class Auth extends Base
{
    #region start
    protected $mUser = null;
    protected $mAuthMenu = null;

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->_checkLogin();
        if ($this->mUser['admin']['is_root'] !== 1) {
            $this->_checkAuth();
        }
        $tree = AuthCache::getAuthRulesTree(1);
        $this->mAuthMenu = $tree->DeepTree();
        $this->assign('_user', $this->mUser);
        $this->assign('_menu', $this->mAuthMenu);
    }

    private function _checkLogin()
    {
        $token = Session::get("user_token");
        if (!$token) {
            $this->error("请先登陆系统后操作", url("portal/login"));
        }
        $this->mUser = $token;
    }

    private function _checkAuth()
    {
        $viewAuthChecker = ViewAuth::Instance();
        $rules = [$this->request->module(), $this->request->controller(), $this->request->action()];
        switch ($viewAuthChecker->checkAuth($rules, $this->mUser['id'])) {
            case ViewAuth::$AUTH_CODES['success']:
                break;
            case ViewAuth::$AUTH_CODES['not_found']:
                $this->error("对不起,未知的请求权限访问");
                break;
            case ViewAuth::$AUTH_CODES['denial']:
                $this->error("对不起,权限拒绝访问");
                break;
            default:
                break;
        }


    }

    #endregion

    public function index()
    {
        $rules = model("auth_rule")->where([])->select();

        return $this->fetch();
    }

    public function authRule()
    {


    }
}