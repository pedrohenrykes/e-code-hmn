<?php

class  GeraExamesRealizados  extends TPage{
  private $form;
  private $datagrid;
  private $pageNavigation;
  private $loaded;

  public function __construct()
  {
    parent::__construct();

    $this->form = new BootstrapFormBuilder( "form_relatorio" );
    $this->form->setFormTitle( "Relatório de Exames Realizados" );
    $this->form->class = "tform";

    $situacao = new TCombo( 'situacao' );

    TTransaction::open('database');
    $repository = new TRepository('VwBauPacientesRecord');
    $criteria = new TCriteria;
    $criteria->setProperty('order', 'situacao');

    $cadastros = $repository->load($criteria);

    foreach ($cadastros as $object) {
      $items[$object->situacao] = $object->situacao;
    }
    $items['TODOS'] = 'TODOS';

    $situacao->addItems($items);
    TTransaction::close(); 

    $situacao->setDefaultOption( "..::SELECIONE::.." );

    $this->form->addFields([new TLabel("situacao") ],[$situacao]);
    $this->form->addAction( "Gerar", new TAction( [ $this, "onGenerate" ] ), "fa:table blue" );
    $situacao->addValidation('situacao', new TRequiredValidator);

    $this->pageNavigation = new TPageNavigation();
    $this->pageNavigation->setAction( new TAction( [ $this, "onGenerate" ] ) ); 

    $container = new TVBox();
    $container->style = "width: 90%";
    $container->add( $this->form );
    $container->add( $this->pageNavigation );
    parent::add( $container );
  }

  function onGenerate(){

    try{
      new RelatorioBauAtendidoPDF();         
    }catch( Exception $e ){
      new TMessage( 'error', $e->getMessage() );
      TTransaction::rollback();
    }
  }

}

?>