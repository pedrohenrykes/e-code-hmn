<?php

class SystemUserList extends TStandardList
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;

    public function __construct()
    {
        parent::__construct();

        parent::setDatabase('database');
        parent::setActiveRecord('SystemUser');
        parent::setDefaultOrder('id', 'asc');
        parent::addFilterField('id', '=', 'id');
        parent::addFilterField('name', 'like', 'name');
        parent::addFilterField('email', 'like', 'email');
        parent::addFilterField('active', '=', 'active');

        $this->form = new BootstrapFormBuilder('form_search_SystemUser');
        $this->form->setFormTitle(_t('Users'));

        $id = new TEntry('id');
        $name = new TEntry('name');
        $email = new TEntry('email');
        $active = new TCombo('active');

        $active->addItems( [ 'Y' => _t('Yes'), 'N' => _t('No') ] );

        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel(_t('Name'))], [$name] );
        $this->form->addFields( [new TLabel(_t('Email'))], [$email] );
        $this->form->addFields( [new TLabel(_t('Active'))], [$active] );

        $id->setSize('30%');
        $name->setSize('70%');
        $email->setSize('70%');
        $active->setSize('70%');

        $this->form->setData( TSession::getValue('SystemUser_filter_data') );

        $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addAction(_t('New'),  new TAction(array('SystemUserForm', 'onEdit')), 'bs:plus-sign green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', 'Id', 'center', 50);
        $column_name = new TDataGridColumn('name', _t('Name'), 'left');
        $column_login = new TDataGridColumn('login', _t('Login'), 'left');
        $column_medico = new TDataGridColumn('medico_nome', 'Medico', 'left');
        $column_email = new TDataGridColumn('email', _t('Email'), 'left');
        $column_active = new TDataGridColumn('active', _t('Active'), 'center');

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_login);
        $this->datagrid->addColumn($column_medico);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_active);

        $column_active->setTransformer( function($value, $object, $row) {
            $class = ($value=='N') ? 'danger' : 'success';
            $label = ($value=='N') ? _t('No') : _t('Yes');
            $div = new TElement('span');
            $div->class="label label-{$class}";
            $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
            $div->add($label);
            return $div;
        });

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $order_name = new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $column_name->setAction($order_name);

        $order_login = new TAction(array($this, 'onReload'));
        $order_login->setParameter('order', 'login');
        $column_login->setAction($order_login);

        $order_medico = new TAction(array($this, 'onReload'));
        $order_medico->setParameter('order', 'medico_id');
        $column_medico->setAction($order_medico);

        $order_email = new TAction(array($this, 'onReload'));
        $order_email->setParameter('order', 'email');
        $column_email->setAction($order_email);

        $action_edit = new TDataGridAction(array('SystemUserForm', 'onEdit'));
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);

        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);

        $action_onoff = new TDataGridAction(array($this, 'onTurnOnOff'));
        $action_onoff->setButtonClass('btn btn-default');
        $action_onoff->setLabel(_t('Activate/Deactivate'));
        $action_onoff->setImage('fa:power-off fa-lg orange');
        $action_onoff->setField('id');
        $this->datagrid->addAction($action_onoff);

        $this->datagrid->createModel();
        $this->datagrid->disableDefaultClick();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid));
        $container->add($this->pageNavigation);

        parent::add($container);
    }

    public function onTurnOnOff( $param )
    {
        try {

            TTransaction::open( 'database' );
            $user = SystemUser::find( $param['id'] );

            if ( $user instanceof SystemUser ) {
                $user->active = $user->active == 'Y' ? 'N' : 'Y';
                $user->store();
            }

            TTransaction::close();

            $this->onReload( $param );

        } catch ( Exception $e ) {

            new TMessage( 'error', $e->getMessage() );

            TTransaction::rollback();

        }
    }
}
