<?php

class FarmaciaProdutoForm extends TWindow
{

    private $form;

    public function __construct()
    {

        parent::__construct();
        parent::setTitle( "Cadastro de Produtos " );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder('form_produtos');
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id = new THidden('id');
        $unidadesaude_id = new THidden('unidadesaude_id');
        $nomeproduto = new TEntry('nomeproduto');
        $tipoproduto = new TEntry('tipoproduto');
        $codigobarra = new TEntry('codigobarra');
        $estoqueminimogeral = new TEntry('estoqueminimogeral');
        $situacao = new TCombo('situacao');
        $medicamento_id = new TDBCombo("medicamento_id", "database", "MedicamentoRecord", "id", "nomemedicamento", "nomemedicamento" );

        $situacao->setDefaultOption('::..SELECIONE..::');
        $situacao->addItems([ 'ATIVO'=>'ATIVO','INATIVO'=>'INATIVO']);

        $nomeproduto->setProperty("title", "O campo é obrigatório");
        $unidadesaude_id ->setProperty("title", "O campo é obrigatório");

        $nomeproduto->setSize("38%");
        $unidadesaude_id->setSize("38%");
        $situacao->setSize("38%");
        $tipoproduto->setSize("38%");
        
        $nomeproduto->addValidation( TextFormat::set( "Nome da Farmácia " ), new TRequiredValidator );
        $situacao->addValidation( TextFormat::set( "Situação" ), new TRequiredValidator );
        $tipoproduto->addValidation( TextFormat::set( "Tipo Farmacia" ), new TRequiredValidator );
        $codigobarra->addValidation( TextFormat::set( "Código de Barra" ), new TRequiredValidator );
        $estoqueminimogeral->addValidation( TextFormat::set( "Estoque Mínimo Geral" ), new TRequiredValidator );

        $this->form->addFields([new TLabel("Nome do Produto: $redstar")], [$nomeproduto]);
        $this->form->addFields([new TLabel("Tipo do Produto: $redstar")], [$tipoproduto]);
        $this->form->addFields([new TLabel("Código de Barras: $redstar")], [$codigobarra]);
        $this->form->addFields([new TLabel("Estoque Mínimo: $redstar")], [$estoqueminimogeral]);
        $this->form->addFields([new TLabel("Situação: $redstar")], [$situacao]);
        $this->form->addFields([new TLabel("Medicamento: $redstar")], [$medicamento_id]);
        $this->form->addFields( [$id, $unidadesaude_id]);


        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        parent::add( $container );

    }

    public function onSave()
    {

        try {

            $this->form->validate();

            TTransaction::open('database');
            $object = $this->form->getData('FarmaciaProdutoRecord');
            //$object->unidadesaude_id = TSession::getValue('profissionalid');
            $object->unidadesaude_id = 1;
            $object->store();
            TTransaction::close();

            $action = new TAction( [ "FarmaciaProdutoList", "onReload" ] );
            new TMessage("info", "Registro salvo com sucesso!", $action);

        } catch (Exception $ex) {

            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );
        }
    }

    function onEdit($param)
    {

        try {

            if (isset($param['key'])) {

                TTransaction::open( "database" );

                $object = new FarmaciaProdutoRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }

    }


}
