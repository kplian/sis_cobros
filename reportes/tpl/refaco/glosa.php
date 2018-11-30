<font size="8">
	<table width="100%" cellpadding="5px"  rules="cols" border="1">
	<tbody>	
		<tr>
		   <td width="35%" ><b>Nro Factura:</b>&nbsp;&nbsp;&nbsp;&nbsp;  <?php  echo $this->cabecera[0]['nro_documento']; ?></td>
			
			<td width="35%">
			<table width="100%" cellpadding="0px"  rules="cols" border="0">
				
				<tr>
					<td width="100%"><b>NIT:</b>&nbsp;&nbsp;&nbsp;&nbsp;  <?php  echo $this->cabecera[0]['nit']; ?></td>
				</tr>
						
			</table>
			</td>
			<td width="30%">
			<table width="80%" cellpadding="0px"  rules="cols" border="0">
				
				<tr>
					<td width="100%"><b>Nro Contrato</b>&nbsp;&nbsp;&nbsp;&nbsp;  <?php  echo $this->cabecera[0]['nro_contrato']; ?></td>
				</tr>
						
			</table>
			</td>
		</tr>
				
		<tr>
			<td><b>Razon Social:</b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo $this->cabecera[0]['razon_social']; ?></td>
			
			<td  colspan="2"><b>Fecha Factura :</b>&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo $this->cabecera[0]['fecha']; ?> </td>
		</tr>
		<tr>
			<td>
				<b>Importe Factura :</b>&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo number_format($this->cabecera[0]['importe_doc'],2,".", ","); echo ' '; echo $this->cabecera[0]['desc_moneda'];?> </td>
			
			<td colspan="2">
				<b>Importe por Cobrar :</b>&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo number_format($this->cabecera[0]['importe_pendiente'],2,".", ","); echo ' '; echo $this->cabecera[0]['desc_moneda'];?> <br>
				<b>Importe por Retencion Garantias :</b>&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo number_format($this->cabecera[0]['importe_retgar'],2,".", ","); echo ' '; echo $this->cabecera[0]['desc_moneda'];?><br>
				<b>Importe por Anticipos :</b>&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo number_format($this->cabecera[0]['importe_anticipo'],2,".", ","); echo ' '; echo $this->cabecera[0]['desc_moneda'];?>
				
			</td>
		</tr>
	
		</tbody>
	</table>
</font>