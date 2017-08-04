<?php
/*
 * @author Pedro Henrique
 * @date 28/04/2016
 */

//ini_set ( 'display_errors', 1 );
//ini_set ( 'display_startup_erros', 1 );
//error_reporting ( E_ALL );

class RequiredTextFormat
{
  private $html;
  private $value;
  private $color;
  private $weight;

  public function __construct($param)
  {
    $this->value  = $param[0];
    $this->color  = $param[1];
    $this->weight = $param[2];
  }

  public function getText()
  {
    $this->html = '<font style="color:' .
    $this->color . ';font-weight:' .
    $this->weight . ';">' .
    $this->value . '</font>' .
    ' deve ser preenchido corretamente, pois ';

    return $this->html;
  }
}
