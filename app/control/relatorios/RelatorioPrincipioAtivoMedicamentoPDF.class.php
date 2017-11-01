<?php

//use FDPF;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;

class RelatorioMedicamentoAplicadoPDF extends FPDF {

    function Header() {
        $this->Image("app/images/sponsors/hmn.jpg", 8, 15, 28, 10);

        $this->SetFont('Arial', 'B', 12);
        $this->SetY("15");
        $this->SetX("35");
        $this->Cell(0, 5, utf8_decode("RELATORIO DE MEDICAMENTOS APLICADOS"), 0, 1, 'L');
        $this->SetX("35");
        $this->Cell(0, 5, utf8_decode("HOSPITAL MUNICIPAL DE NATAL"), 0, 1, 'L');


        $this->Ln(6);

        $this->ColumnHeader();
    }

    function ColumnHeader() {

        $this->SetFont('Arial', 'B', 10);


        $this->Cell(25 , 5, utf8_decode("Atendimento"), 1, 0, 'L');
        $this->Cell(30 , 5, utf8_decode("Nome Paciente"), 1, 0, 'L');
        $this->Cell(25 , 5, utf8_decode("Medicamento"), 1, 0, 'L');
        $this->Cell(25 , 5, utf8_decode("Data Entrada"), 1, 0,'L');
        $this->Cell(25 , 5, utf8_decode("Hora Entrada"), 1, 0, 'L');
        $this->Cell(30 , 5, utf8_decode("Queixa Principal"), 1, 0, 'L');
        $this->Cell(30 , 5, utf8_decode("Data Nascimento"), 1, 0, 'L');
        $this->Cell(10 , 5, utf8_decode("Sexo"), 1, 0, 'L');
        $this->Cell(33 , 5, utf8_decode("Grupo Sanguineo"), 1, 0, 'L');
        $this->Cell(20 , 5, utf8_decode("Fator RH"), 1, 0, 'L');
        $this->Cell(20 , 5, utf8_decode("Aplicação"), 1, 1, 'L');







    }

    function ColumnDetail() {
        $situacao =  $_REQUEST['situacao'];
        $total = 0;

        TTransaction::open('database');
        $repository = new TRepository('VwMedicamentoAplicadoRecord');

        $criteria = new TCriteria;

        $criteria->setProperty('order', 'dataentrada', 'asc');
        if ($situacao != 'TODOS'){
            $criteria->add(new TFilter('situacao', '=', $situacao ));

    }
    $rows = $repository->load($criteria);

    if ($rows) {

        foreach ($rows as $row) {

            $this->SetFont('Arial', '', 8);
            $this->Cell(25, 5, utf8_decode($row->atendimento), 0, 0, 'L');
            $this->Cell(30, 5, utf8_decode($row->nomepaciente), 0, 0, 'L');
            $this->Cell(25, 5, utf8_decode($row->medicamento), 0, 0, 'L');
            $this->Cell(25, 5, utf8_decode($row->dataentrada), 0, 0, 'L');
            $this->Cell(25, 5, utf8_decode($row->horaentrada), 0, 0, 'L');
            $this->Cell(30, 5, utf8_decode($row->queixaprincipal), 0, 0, 'L');
            $this->Cell(30, 5, utf8_decode($row->datanascimento), 0, 0, 'L');
            $this->Cell(10, 5, utf8_decode($row->sexo), 0, 0, 'L');
            $this->Cell(33, 5, utf8_decode($row->gruposanguineo), 0, 0, 'L');
            $this->Cell(20, 5, utf8_decode($row->fatorrh), 0, 0, 'L');
            $this->Cell(20, 5, utf8_decode($row->aplicacao), 0, 1, 'L');
            $this->Cell(0, 0, '', 1, 1, 'L');

            $total++;
        }

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 5, utf8_decode('Total:  ' .$total . ' Pacientes'), 0, 1, 'L');


    }

    TTransaction::close();
}

    function Footer() {

    }
}


$pdf = new RelatorioMedicamentoAplicadoPDF("L", "mm", "A4");
$pdf->SetTitle("RELATÓRIO DE PACIENTES POR DATA - HMN");
$pdf->SetSubject("RELATÓRIO DE PACIENTES POR DATA - HMN");


$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', '', 12);
$pdf->ColumnDetail();
//$file = "app/reports/RelatorioMedMaisUtilizadoPDF".$_SESSION['medico_id'].".pdf";
$file = "app/reports/RelatorioMedicamentoAplicadoPDF.pdf";

//abrir pdf
$pdf->Output($file);
$pdf->openFile($file);


?>
