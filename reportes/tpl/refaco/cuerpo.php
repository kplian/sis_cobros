<font size="8">
	<table width="100%" cellpadding="5px"  rules="cols" border="1">
	<tbody>	
		<tr>
			<td width="5%" ><b>Nro:</b> 
				
			</td>
			<td width="10%" ><b>Fecha Cobro:</b> 
				
			</td>
			<td width="20%" ><b>Nro tramite Cobro:</b>&nbsp;&nbsp;&nbsp;&nbsp; 
				
			</td>
			<td width="20%" ><b>Importe de Cobro:</b>&nbsp;&nbsp;&nbsp;&nbsp; 
				
			</td>
			  <td width="10%"><b>Moneda:</b>&nbsp;&nbsp;&nbsp;&nbsp;
		   	
		   </td>
			
		   <td width="20%"><b>Cobro a Factura(BS):</b>&nbsp;&nbsp;&nbsp;&nbsp;
		   	
		   </td>
		    <td width="15%"><b>Saldo (BS):</b>&nbsp;&nbsp;&nbsp;&nbsp;
		   	
		   </td>
		   	
		 
		
			
		</tr>
		<?php 
		$i=0;
		$u=1;
		$saldo=$this->cabecera[0]['importe_pendiente']+$this->cabecera[0]['importe_retgar']+$this->cabecera[0]['importe_anticipo'];
		while ($this->cabecera[$i]['id'] != null) {
			$cobroFactura= $this->cabecera[$i]['importe_cobro_factura']; 
			$saldo=$saldo-$cobroFactura;
			 
		 ?>
		<tr> 
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo $u ?></td>		
			<td  ><b></b><?php  echo $this->cabecera[$i]['fecha_cobro']; ?></td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo $this->cabecera[$i]['nro_tramite_cobro']; ?> </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo number_format($this->cabecera[$i]['importe_cobro'],2,".", ","); ?></td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo $this->cabecera[$i]['desc_moneda_cobro']; ?></td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo number_format($this->cabecera[$i]['importe_cobro_factura'],2,".", ","); ?> </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo number_format($saldo,2,".", ",");; ?> </td>
		</tr>
		
		<?php 
		$u++;
	    $i++;  
		} ?>
		<tr> 
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b>Total Cobrado:</b>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo number_format($this->cabecera[0]['importe_cobrado_mb'],2,".", ","); ?> </td>
			<td  ><b>Saldo por Cobrar:</b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo number_format($this->cabecera[0]['saldo_por_cobrar'],2,".", ","); ?> </td>
		</tr>
	 
	
	</tbody>
	</table>
</font>