<?php
class AtendimentoDetalhe extends TStandardList{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;
    function __construct(){
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_atendimento_paciente');
        $this->form->setFormTitle('Atendimento Realizados');
        
        parent::setDatabase('database');
        parent::setActiveRecord('AtendimentoRecord');
        
        $id             = new THidden('id');
        $paciente_id    = new THidden('paciente_id');
        $agenda      = new THidden('agenda_id');
        $docente   = new TCombo('docente_id');
        $datainicio          = new TDate('datainicio');

        $horainicio = new THidden('horainicio');
        $datafim = new TDate('datafim');
        $horafim = new THidden('horafim');

       $paciente_id->setValue(filter_input(INPUT_GET, 'fk'));
        TTransaction::open('database');
     //  $visita = new PacienteRecord( filter_input( INPUT_GET, 'fk' ) );
       // if( $visita ){
           // $paciente_nome = new TLabel( $visita->nome );
           // $paciente_nome->setEditable(FALSE);
       // }
        TTransaction::close();
        
        $items = array();
        TTransaction::open('database');
        $repository = new TRepository('DocenteRecord');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nomedocente');
        $cadastros = $repository->load($criteria);
        foreach ($cadastros as $object) {
            $items[$object->id] = $object->nomedocente;
        }
        $docente->addItems($items);
        TTransaction::close();
        
        $datainicio->setMask('dd/mm/yyyy');
        $datainicio->setValue(date("d/m/Y"));
        $datainicio->setDatabaseMask('yyyy-mm-dd');
        $horainicio->setValue(date('H:i:s'));
        $docente->setDefaultOption('::..SELECIONE..::');
        
        $datafim->setMask('dd/mm/yyyy');
        //$datafim->setValue(date("d/m/Y"));
        $datafim->setDatabaseMask('yyyy-mm-dd');
        $horafim->setValue(date('H:i:s'));

        $datainicio->addValidation( "Data do Exame", new TRequiredValidator );
        //$this->form->addFields( [new TLabel('Paciente: '), $paciente_nome] );
        $this->form->addFields( [new TLabel('Docente <font color=red><b>*</b></font>')], [$docente] );
        $this->form->addFields( [new TLabel('Data Inicio')],[$datainicio]  );
        $this->form->addFields( [new TLabel('Data Fim')],[$datafim]  );



       // $this->form->addFields( [ $id, $paciente_id ,$horainicio,$horafim] );
        $action = new TAction(array($this, 'onSave'));
        $action->setParameter('id', '' . filter_input(INPUT_GET, 'id') . '');
        $action->setParameter('fk', '' . filter_input(INPUT_GET, 'fk') . '');
        $this->form->addAction('Salvar', $action, 'fa:floppy-o');
       // $this->form->addAction('Voltar para Agendamento',new TAction(array('AgendamentoDetalhe','onReload')),'fa:table blue');
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        $column_1 = new TDataGridColumn('paciente_nome', 'Paciente', 'left');
        $column_2 = new TDataGridColumn('docente_nome', 'Exame', 'left');
        $column_3 = new TDataGridColumn('horainicio','Hora Inicio','left');
    
        $this->datagrid->addColumn($column_1);
        $this->datagrid->addColumn($column_2);
        $this->datagrid->addColumn($column_3);
        
        $edit = new TDataGridAction( [ $this, "onEdit" ] );
        $edit->setButtonClass( "btn btn-default" );
        $edit->setLabel( "Editar" );
        $edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $edit->setField( "id" );
        $edit->setParameter('fk', filter_input(INPUT_GET, 'fk'));
        $this->datagrid->addAction( $edit );
        $del = new TDataGridAction(array($this, 'onDelete'));
        $del->setButtonClass('btn btn-default');
        $del->setLabel(_t('Delete'));
        $del->setImage('fa:trash-o red fa-lg');
        $del->setField('id');
        $del->setParameter('fk', filter_input(INPUT_GET, 'fk'));
        $this->datagrid->addAction($del);
        
        $this->datagrid->createModel();
        
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
    function onEdit( $param ){
        try{
            if( isset( $param[ "key" ] ) ){
                TTransaction::open( "database" );
                $object = new AtendimentoRecord( $param[ "key" ] );
                $this->form->setData( $object );
                TTransaction::close();
            }
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );
        }
    }
    public function onSave($param = NULL){
        try{
            TTransaction::open('database');
            $cadastro = $this->form->getData('AtendimentoRecord');
            $this->form->validate();
            $cadastro->store();
            TTransaction::close();
            $param=array();
            $param['key'] = $cadastro->id;
            $param['id'] = $cadastro->id;
            $param['fk'] = $cadastro->paciente_id;
            $param['fk'] = $cadastro->docente_id;
            $param['fk'] = $cadastro->agenda_id;
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            TApplication::gotoPage('AtendimentoDetalhe','onReload', $param); 
        }catch (Exception $e){
            $object = $this->form->getData($this->activeRecord);
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    public function onReload( $param = NULL ){
        try{
            TTransaction::open( "database" );
            $repository = new TRepository( "AtendimentoRecord" );
            if ( empty( $param[ "order" ] ) )
            {
                $param[ "order" ] = "id";
                $param[ "direction" ] = "asc";
            }
            $limit = 10;
            
            $criteria = new TCriteria();
           // $criteria->add(new TFilter('paciente_id', '=', filter_input(INPUT_GET, 'fk')));
            $criteria->setProperties( $param );
            $criteria->setProperty( "limit", $limit );
            
            $objects = $repository->load( $criteria, FALSE );
            $this->datagrid->clear();
            if ( !empty( $objects ) ){
                foreach ( $objects as $object ){
                    $object->dataexame = TDate::date2br($object->dataexame);
                    $this->datagrid->addItem( $object );
                }
            }
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            $this->pageNavigation->setCount($count); 
            $this->pageNavigation->setProperties($param); 
            $this->pageNavigation->setLimit($limit);
            TTransaction::close();
            $this->loaded = true;
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();
            new TMessage( "error", $ex->getMessage() );
        }
    }
        
    
}