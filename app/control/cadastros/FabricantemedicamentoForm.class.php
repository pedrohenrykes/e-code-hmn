<?php



class FabricantemedicamentoForm extends TWindow
{

    private $form;

    public function __construct()
    {

        parent::__construct();
        parent::setTitle( "Fabricante de Medicamentos" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_fabricantemedicamento" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id             = new THidden( "id" );
        $nomefabricante = new TEntry("nomefabricante");
        $cnpj           = new TEntry("cnpj");

        $cnpj->setProperty("title", "O campo e obrigatorio");
        $nomefabricante->setProperty("title", "Digite o nome do Fabricante");

        $nomefabricante->setSize("38%");
        $cnpj->setSize("38%");

        $this->form->addFields([new TLabel("Fabricante de Medicamentos: $redstar")], [$nomefabricante]);
        $this->form->addFields([new TLabel("CNPJ: $redstar")], [$cnpj]);
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "FabricantemedicamentoList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );
            $object = $this->form->getData("FabricantemedicamentoRecord");
            $object->store();
            TTransaction::close();

            $action = new TAction( [ "FabricantemedicamentoList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public function onEdit( $param )
    {
        try {

            if( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );
                $object = new TipoProfissionalRecord($param["key"]);
                $this->form->setData($object);
                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
