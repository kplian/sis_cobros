<font size="8">
<?php 	
$t=0;
$primero=0;
 while ($this->cabecera[$t]['id']!=null) {
     
 if ($primero != $this->cabecera[$t]['id_doc_compra_venta']){
	?>
	<br>
	<table width="100%" cellpadding="5px"  rules="cols" border="1">
	<tbody>	
			
		<tr>
			<td width="33%" ><b>Nro Factura:</b> 
				<?php echo $this->cabecera[$t]['nro_documento'];?>
				
			</td>
			<td width="33%" ><b>Fecha Factura</b> 
				<?php echo $this->cabecera[$t]['fecha'];?>
				
			</td>
			<td width="34%" ><b>Importe Factura</b> 
				<?php echo $this->cabecera[$t]['importe_doc'];?>
				
			</td>
			
			
		</tr>
	
		<tr>
			<td width="5%" ><b>Nro:</b> 
				
			</td>
			<td width="10%" ><b>Fecha cobro:</b> 
				
			</td>
			<td width="20%" ><b>Nro Tramite cobro:</b>&nbsp;&nbsp;&nbsp;&nbsp; 
				
			</td>
			<td width="20%" ><b>Importe de Cobro:</b>&nbsp;&nbsp;&nbsp;&nbsp; 
				
			</td>
			  <td width="10%"><b>Moneda:</b>&nbsp;&nbsp;&nbsp;&nbsp;
		   	
		   </td>
			
		   <td width="20%"><b>Importe Cobrado(BS):</b>&nbsp;&nbsp;&nbsp;&nbsp;
		   	
		   </td>
		    <td width="15%"><b>Saldo por Cobrar (BS):</b>&nbsp;&nbsp;&nbsp;&nbsp;
		   	
		   </td>	
			
		</tr>
		<?php 
		$i=0;
		$saldo=$this->cabecera[$t]['importe_doc'];
		$primeroS=$this->cabecera[$t]['id_doc_compra_venta'];
		
		while ($this->cabecera[$i]['id']!=null) {
				
			if($primeroS == $this->cabecera[$i]['id_doc_compra_venta']) {
			$cobroFactura= $this->cabecera[$i]['importe_cobro_factura'];
			$saldo=$saldo-$cobroFactura;
		 ?>
		<tr> 
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo $this->cabecera[$i]['id_doc_compra_venta']; ?></td>		
			<td  ><b></b><?php  echo $this->cabecera[$i]['fecha_cobro']; ?></td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo $this->cabecera[$i]['nro_tramite_cobro']; ?> </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo number_format($this->cabecera[$i]['importe_cobro'],2,".", ","); ?></td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo $this->cabecera[$i]['desc_moneda_cobro']; ?></td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo number_format($this->cabecera[$i]['importe_cobro_factura'],2,".", ","); ?> </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo number_format($saldo,2,".", ",");; ?> </td>
		</tr>
			
		<?php 
		
			}
		
	    $i++;  
	
			}?>
		
	
		<tr> 
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b>Total Cobrado:</b>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo number_format($this->cabecera[$t]['importe_cobrado_mb'],2,".", ","); ?> </td>
			<td  ><b>Saldo por Cobrar:</b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo number_format($this->cabecera[$t]['saldo_por_cobrar'],2,".", ","); ?> </td>
		</tr>
	
		
	 
	
	</tbody>
	</table>
	
	<br>
	<br>
	<?php 
 		}
	$primero=$this->cabecera[$t]['id_doc_compra_venta'];
	$t++; 
	
	}
		 ?> 
