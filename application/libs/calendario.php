<?php
class calendario 
{
    private $_dia;
    private $_mes;
    private $_ano;
    private $_sem;
    // variable para conexion a datos
    private $_cnx;
    private $_url;
    private $_tcell;
    private $_planificacion = array();
    private $_eventos = array();
    
    protected $_semana = array("lunes","martes","miercoles","jueves","viernes","sabado","domingo");
    protected $_meses = array('01'=>"enero",'02'=>"febrero",'03'=>"marzo",
                            '04'=>"abril",'05'=>"mayo",'06'=>"junio",
                            '07'=>"julio",'08'=>"agosto",'09'=>"septiembre",
                            '10'=>"octubre",'11'=>"noviembre",'12'=>"diciembre");
    
    public function __construct($mes=false,$ano=false) {
        
        $this->_dia = date('d',time());
        
        if($mes)
            $this->_mes = $mes;
        else
            $this->_mes = date('m',time());
        
        if($ano)
            $this->_ano = $ano;
        else
            $this->_ano = date('Y',time());
        
        
        $this->_tcell = array("anc"=>40,"alt"=>70);
    }
    //--------------------------------------------------------------------------
    //  METODOS DE ASIGNACION
    //--------------------------------------------------------------------------
    public function setDia($valor)
    {
        $this->_dia = $valor;
    }
    public function setMes($valor)
    {
        $this->_mes = $valor;
    }
    public function setAno($valor)
    {
        $this->_ano = $valor;
    }
    public function setSemana($valor)
    {
        $this->_sem = $valor;
    }
    public function setConexion($valor)
    {
        $this->_cnx = $valor;
    }
    public function setUrl($valor)
    {
        $this->_url = $valor;
    }
    public function setPlanificacion($valor)
    {
        $this->_planificacion = $valor;
    }
    public function setEventos($valor)
    {
        $this->_eventos = $valor;
    }            
    //--------------------------------------------------------------------------
    //  METODO DE EXTRACCION
    //--------------------------------------------------------------------------
    public function getDia()
    {
        return $this->_dia;
    }
    public function getMes()
    {
        return $this->_mes;
    }
    public function getAno()
    {
        return $this->_ano;
    }
    public function getSemana()
    {
        return $this->_sem;
    }
    
    //--------------------------------------------------------------------------
    //  RETORNA LA REPRESENTACION NUMERICA  DEL  DIA DE LA SEMANA 1 AL 7
    //--------------------------------------------------------------------------
    private function diaSemana($dia = FALSE)
    {
        if(!$dia)
            $dia = $this->_dia;
        return date('N', strtotime($this->_ano.'-'.$this->_mes.'-'.$dia));
    }
    //--------------------------------------------------------------------------
    //  RETORNA EL NUMERO DE DIAS QUE TIENE EL MES DADO
    //--------------------------------------------------------------------------
    private function diasMes($fecha = FALSE)
    {
        if($fecha)
            return date("t",strtotime($fecha));
        else
            return date("t",strtotime(date('Y-m-d')));
    }
    //--------------------------------------------------------------------------
    //  RETORNA FECHA RESTANDOLE LA CANTIDAD DE MESES PASADOS POR PARAMETRO
    //--------------------------------------------------------------------------
    public function restarMes($meses)
    {
        $fecha = $this->_ano.'-'.  $this->_mes .'-'. date('d');
        $nuevafecha = strtotime ('-'.$meses.' month' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
        return $nuevafecha;
    }
    private function siguiente()
    {
        $fecha = $this->_ano.'-'.  $this->_mes .'-'. date('d');
        $nuevafecha = strtotime ('+1 month' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'm/Y' , $nuevafecha );
        return $nuevafecha;
    }
    private function anterior()
    {
        $fecha = $this->_ano.'-'.  $this->_mes .'-'. date('d');
        $nuevafecha = strtotime ('-1 month' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'm/Y' , $nuevafecha );
        return $nuevafecha;
    }
    //--------------------------------------------------------------------------
    //  RETORNA FECHA SUMANDOLE LA CANTIDAD DE MESES PASADOS POR PARAMETRO
    //--------------------------------------------------------------------------
    public function sumarMes($meses)
    {
        $fecha = $this->_ano.'-'.  $this->_mes .'-'. date('d');
        $nuevafecha = strtotime ('+'.$meses.' month' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
        return $nuevafecha;
    }
    //--------------------------------------------------------------------------
    //  RETORNA EL NUMERO DE SEMANA DEL MES ACTUAL
    //--------------------------------------------------------------------------
    private function semanaMes()
    {
        return date("W") - date("W",strtotime($this->_ano."-".$this->_mes."-".$this->_dia)) + 1;
    }        
    
    //--------------------------------------------------------------------------
    //  CREA ARREGLO CALENDARIO ESTRUCTURA PARA LUEGO PINTAR
    //--------------------------------------------------------------------------
    private function crear()
    {
        $calendario = array();
        $week = 1;
        $total_dias = $this->diasMes();
        $primer_dia = $this->diaSemana(1);
        $ultimo_dia = $this->diaSemana($total_dias);
        if($primer_dia > 1)
        {
            //----------------------------------------------
            //si tiene que tomar ddel mes aNTERIOR
            //----------------------------------------------
            $fecha_anterior = $this->restarMes(1);
            $mes_anterior = date('m',  strtotime($fecha_anterior));
            $ano_anterior = date('Y',  strtotime($fecha_anterior));
            $tdi = ($primer_dia-1);
            $di = ($this->diasMes($fecha_anterior)+1)-$tdi;
            
            $day_week = 1;
            for($i = $di;$i <= ($di+$tdi);$i++)
            {
                $calendario[$week][$day_week] = $ano_anterior.'-'.$mes_anterior.'-'.$i;
                $day_week ++;
            }
   
        }    
        
        for($i=1;$i <= date('t',strtotime($this->_ano."-".$this->_mes."-01"));$i++)
        {
            $dia_act = (strlen($i)< 2)?'0'.$i:$i;
            $day_week = $this->diaSemana($i);
            $calendario[$week][$day_week] = $this->_ano.'-'.$this->_mes.'-'.$dia_act;
            if ($day_week == 7)
                $week++; 
            
        }
        //----------------------------------------------------------------------
        // si tiene que tomar del mes siguiente
        //----------------------------------------------------------------------
        if($ultimo_dia < 7)
        {
            $fecha_proxima = $this->sumarMes(1);
            $mes_proximo = date('m',  strtotime($fecha_proxima));
            $ano_proximo = date('Y', strtotime($fecha_proxima));
           $tdf = (7-$ultimo_dia);
           //die($tdf);
           for($i = 1;$i <= $tdf;$i++)
            {
                $calendario[$week][$day_week+$i] = $ano_proximo.'-'.$mes_proximo.'-'.$i;                
            }
        }
        
        return $calendario;
    }
    
    //--------------------------------------------------------------------------
    // IMPRIME CALENDARIO POR PANTALLA
    //--------------------------------------------------------------------------
    public function imprimir($atributos=FALSE)
    {
        
        $siguiente = $this->_url.'/'.$this->siguiente();
        
                    
        $anterior =  $this->_url.'/'.$this->anterior();   
        
        $calendario = $this->crear();
        
        $tabla  ="<div class='table-responsive'>";
        $tabla .="<div class='btn-group' role='group' aria-label='...'>";
        $tabla .="<a href='$anterior' class='btn btn-default'><i class='fa fa-chevron-left'></i> Anterior</a>";
        $tabla .="<span class='btn btn-default'>".ucfirst($this->_meses[$this->_mes])."</span>";
        $tabla .="<a href='$siguiente' class='btn btn-default'>Siguiente <i class='fa fa-chevron-right'></i></a>";
        $tabla .="</div>";
        $tabla .= "<table $atributos>";
        $tabla .="<thead>";
        $tabla .="<tr>";
        foreach($this->_semana as $val)
        {
            $tabla .="<td><strong>".ucfirst($val)."</strong></td>";
        }    
        $tabla .="</tr>";

        $tabla .="</thead><tbody>";
        $j = 0;
        $planificacion=0;
       // print_r($this->_calendar);
         //           exit();
        foreach ($calendario as $dias)
        {    
                $clase_cell ="class='active'";
                $tabla .="<tr height='".$this->_tcell['alt']."'>";
                for ($i=1;$i<=7;$i++)
                {
                      
                    
                    if(isset($dias[$i]))
                    {
                        $activa=FALSE;
                        if(count($this->_planificacion))
                        {
                            
                            $activa = FALSE;
                            foreach($this->_planificacion as $pln)
                            {
                                                                
                                if(($dias[$i] >=$pln['fecha_inicio'] && $dias[$i]<=$pln['fecha_fin'])&& $pln['condicion_planificacion']=='ACTIVA' )
                                {
                                    if($dias[$i]>= date('Y-m-d') && $dias[$i]<=$pln['fecha_fin'])
                                        $activa = TRUE;
                                    else
                                        $activa = FALSE;
                                        
                                    $planificacion = $pln['id_planificacion'];
                                }else
                                    $activa = FALSE;
                                
                            }
                                
                            
                        }else
                        {
                            $activa = FALSE;     
                        }
                        
                        if($activa)
                        {
                            $clase_guardia="class='glyphicon glyphicon-eye-open'";
                            $clase_reload ="class='fa fa-search'";
                            $clase_cell ="class='info'";
                            
                            $tabla .= "<td $clase_cell >"
                            . "<span class='col-md-2 ' ><p class='text-left lead'>".date('d',strtotime($dias[$i]))."</p></span>"
                            . "<span class='col-md-5'><a href='#' data-toggle='modal' data-id='".$dias[$i]."' data-target='#myModal' class='openModal'  ><i class='fa fa-plus-circle' ></i></a></span>"
                            . "<span class='col-md-5'><a href='#' data-toggle='modal' data-id='".$dias[$i]."' data-target='#myLista' class='openModalLista'  ><i $clase_reload  ></i></a></span>"    
                            . "</td>";
                            
                        }else
                        {
                            $clase_cell ="class='active'";
                            $clase_reload ="class='fa fa-search'";
                            
                            $tabla .= "<td $clase_cell >"
                            . "<span class='col-md-2 ' ><p class='text-left lead'>".date('d',strtotime($dias[$i]))."</p></span>"
                            . "<span class='col-md-5'><i class='fa fa-plus-circle' ></i></span>"
                            . "<span class='col-md-5'><a href='#' data-toggle='modal' data-id='".$dias[$i]."' data-target='#myLista' class='openModalLista'  ><i $clase_reload  ></i></a></span>"    
                            . "</td>";
                        }
                        
                         

                        $j++;

                    }else
                    {
                        $tabla .= "<td  >"
                            . "<span class='col-md-1 ' ><p class='text-left lead'></p></span>";
                    }
                }
                $tabla .="</tr>";

            }    
            $tabla .="</tbody></table><input name='planificacion' id='planificacion' type='hidden' value='".$planificacion."' />";        
            $tabla .= "</div>";
                
         return $tabla;       
        
    }         
    
    
            
}