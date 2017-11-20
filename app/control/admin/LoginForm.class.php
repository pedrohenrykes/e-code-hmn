<?php

class LoginForm extends TPage
{
    protected $form;

    function __construct($param)
    {
        parent::__construct();

        $ini  = AdiantiApplicationConfig::get();

        $this->{'style'} .= 'clear:both;text-align:center;';

        $this->form = new BootstrapFormBuilder('form_login');

        $icon = new TImage( 'app/images/system/logo.png' );
        $login = new TEntry('login');
        $password = new TPassword('password');

        $login->setSize('70%', 40);
        $password->setSize('70%', 40);

        $login->setProperty( 'style', 'height:35px; font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;' );
        $password->setProperty( 'style', 'height:35px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;' );

        $login->setProperty( 'placeholder', 'Usuário' );
        $password->setProperty( 'placeholder', 'Senha' );

        $user = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="glyphicon glyphicon-user"></span></span>';
        $locker = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="glyphicon glyphicon-lock"></span></span>';
        $unit = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="fa fa-university"></span></span>';

        $this->form->addFields( [$icon] );
        $this->form->addFields( [$user, $login] );
        $this->form->addFields( [$locker, $password] );

        if (!empty($ini['general']['multiunit']) and $ini['general']['multiunit'] == '1') {
            $unit_id = new TCombo('unit_id');
            $unit_id->setSize('70%');
            $unit_id->setProperty( 'style', 'height:35px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;' );
            $this->form->addFields( [$unit, $unit_id] );
            $login->setExitAction(new TAction( [$this, 'onExitUser'] ) );
        }

        $btn = $this->form->addAction('Entrar', new TAction(array($this, 'onLogin')), '');
        $btn->setProperty( 'class', 'waves-effect waves-light btn btn-primary' );
        $btn->setProperty( 'style', 'height:50px; width:90%; display:block; margin:auto; font-size:17px; border-radius:7px;' );

        $wrapper = new TElement('div');
        $wrapper->{'style'} .= 'margin:auto; margin-top:100px; max-width:460px; border-radius:7px;';
        $wrapper->{'id'} .= 'login-wrapper';
        $wrapper->add($this->form);

        self::runJScript( 'addLoader' );

        parent::add($wrapper);
    }

    public static function onExitUser($param)
    {
        try {

            TTransaction::open('database');

            $user = SystemUser::newFromLogin( $param['login'] );

            if ($user instanceof SystemUser) {

                $units = $user->getSystemUserUnits();
                $options = [];

                if ($units) {
                    foreach ($units as $unit) {
                        $options[$unit->id] = $unit->name;
                    }
                }

                TCombo::reload('form_login', 'unit_id', $options);
            }

            TTransaction::close();

        } catch (Exception $e) {

            new TMessage('error',$e->getMessage());

            TTransaction::rollback();

        }
    }

    public static function onLogin($param)
    {
        self::runJScript( 'addLoader' );

        $ini  = AdiantiApplicationConfig::get();

        try
        {
            TTransaction::open('database');

            $data = (object) $param;

            if (empty($data->login)) {
                throw new Exception( AdiantiCoreTranslator::translate('The field ^1 is required', _t('Login')) );
            }

            if (empty($data->password)) {
                throw new Exception( AdiantiCoreTranslator::translate('The field ^1 is required', _t('Password')) );
            }

            $user = SystemUser::authenticate( $data->login, $data->password );

            if ($user) {

                TSession::regenerate();
                $programs = $user->getPrograms();
                $programs['LoginForm'] = TRUE;

                TSession::setValue('logged', TRUE);
                TSession::setValue('login', $data->login);
                TSession::setValue('userid', $user->id);
                TSession::setValue('usergroupids', $user->getSystemUserGroupIds());
                TSession::setValue('userunitids', $user->getSystemUserUnitIds());
                TSession::setValue('username', $user->name);
                TSession::setValue('usermail', $user->email);
                TSession::setValue('frontpage', '');
                TSession::setValue('programs',$programs);

                if (!empty($user->unit)) {
                    TSession::setValue('userunitid',$user->unit->id);
                }

                if (!empty($ini['general']['multiunit']) and $ini['general']['multiunit'] == '1' and !empty($data->unit_id)) {
                    TSession::setValue('userunitid', $data->unit_id );
                }

                $frontpage = $user->frontpage;
                SystemAccessLog::registerLogin();

                if ($frontpage instanceof SystemProgram AND $frontpage->controller) {
                    AdiantiCoreApplication::gotoPage($frontpage->controller);
                    TSession::setValue('frontpage', $frontpage->controller);
                } else {
                    AdiantiCoreApplication::gotoPage('DashBoardCreate');
                    TSession::setValue('frontpage', 'DashBoardCreate');
                }

                self::runJScript( 'hideForm' );
            }

            TTransaction::close();

        } catch (Exception $e) {

            new TMessage('error',$e->getMessage());

            TSession::setValue('logged', FALSE);

            TTransaction::rollback();

        }

        self::runJScript( 'rmLoader' );
    }

    public static function reloadPermissions()
    {
        try {

            TTransaction::open('database');
            $user = SystemUser::newFromLogin( TSession::getValue('login') );

            if ($user) {

                $programs = $user->getPrograms();
                $programs['LoginForm'] = TRUE;
                TSession::setValue('programs', $programs);

                $frontpage = $user->frontpage;
                if ($frontpage instanceof SystemProgram AND $frontpage->controller) {
                    TApplication::gotoPage($frontpage->controller); // reload
                } else {
                    TApplication::gotoPage('DashBoardCreate'); // reload
                }

            }

            TTransaction::close();

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onLogout()
    {
        SystemAccessLog::registerLogout();
        TSession::freeSession();
        AdiantiCoreApplication::gotoPage('LoginForm', '');
    }

    public static function runJScript( $optins )
    {
        switch( $optins ) {

            case 'addLoader':
                TScript::create('
                    $(document).ready(function(){
                      var oneClick = true;
                      $("#tbutton_btn_entrar").click(function(){
                        if( oneClick ) {
                          $("#login-wrapper").append("
                            <div id=\"login_loader\" class=\"preloader pl-size-xs\">
                              <div class=\"spinner-layer pl-green\">
                                <div class=\"circle-clipper left\"><div class=\"circle\"></div></div>
                                <div class=\"circle-clipper right\"><div class=\"circle\"></div></div>
                              </div>
                            </div>
                          ");
                          oneClick = false;
                        }
                      });
                    });
                ');
                break;

            case 'rmLoader':
                TScript::create('
                    $(document).ready(function(){
                      $("#login_loader").remove();
                    });
                ');
                break;

            case 'hideForm':
                TScript::create('
                    $(document).ready(function(){
                      $("#adianti_div_content").attr(
                        "class", "animated fadeOutDown"
                      );
                    });
                ');
                break;
        }
    }
}
