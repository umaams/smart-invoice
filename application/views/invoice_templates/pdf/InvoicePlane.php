<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php _trans('invoice'); ?></title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">
</head>
<style>
    /* @page :first { margin-top: 0.0cm !important;   }
    @page { margin-top: 0.0cm !important;   } */
    .header-po {
        font-family: Book Antiqua;
        border-collapse: collapse;
        font-weight: normal;
        color: #000000;
    }

    .header-po td,
    .header-po th {
        border: 1px solid #000000;
        padding: 0px;
        font-weight: normal;
    }

    .header-po tr:nth-child(even) {
        background-color: #ffffff;
    }

    .header-po tr:hover {
        background-color: #ffffff;
    }

    .header-po th {
        text-align: center;
        background-color: #ffffff;
        color: #000000;
        font-weight: normal;
    }

    hr {
        height: 2px;
        color: #82A8F4;
        background-color: #82A8F4;
        border: none;
    }
</style>
<body>
    <table class="header-po" width="100%" style="font-family: Book Antiqua;">
        <tr>
            <td width="18%" style="padding: 5px; border: none;"><?php echo invoice_logo_pdf(); ?></td>
            <td class="align-middle" style="vertical-align: middle; padding: 5px; border: none;">
                <b><?=$invoice->user_name?></b>
                <p>Perum Puri Asta Kencana B3 No 16 Menganti - Gresik, 081234271646</p>
                <p>Website : www.linas-media.com</p>
                <p>Supplier, Networking, Web Service, GIS,MIS, IT-Consultant</p>
            </td>
        </tr>
    </table>
    <hr>
    <div width="100%" style="text-align: center; font-family: Book Antiqua; font-size:12px;">
        <h3><u><i>INVOICE</i></u></h3>
    </div>
    <table class="header-po" width="100%" style="font-family: Book Antiqua;">
        <tr>
            <td width="12%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b>Invoice No</b></td>
            <td width="2%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b>:</b></td>
            <td width="86%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b><?=$invoice->invoice_number?></b></td>
        </tr>
        <tr>
            <td width="12%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b>Date</b></td>
            <td width="2%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b>:</b></td>
            <td width="86%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b><?=date('d-m-Y',strtotime($invoice->invoice_date_created))?></b></td>
        </tr>
        <tr>
            <td width="12%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b>Customer</b></td>
            <td width="2%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b>:</b></td>
            <td width="86%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b><?=$invoice->client_name?></b></td>
        </tr>
        <tr>
            <td width="12%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b>PIC Name</b></td>
            <td width="2%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b>:</b></td>
            <td width="86%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b><?=$invoice->client_pic_name?></b></td>
        </tr>
        <tr>
            <td width="12%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b>Address</b></td>
            <td width="2%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b>:</b></td>
            <td width="86%" style="vertical-align: middle; padding: 3px; border: none; font-size:12px;"><b><?=htmlsc($invoice->client_address_1)." ".htmlsc($invoice->client_address_2)." ".htmlsc($invoice->client_city)?></b></td>
        </tr>
    </table>
    <br><br>
    <table class="item-table" style="font-family: Book Antiqua;">
        <thead>
        <tr>
            <th>No</th>
            <th class="item-desc" style="text-align: center;"><?php _trans('description'); ?></th>
            <th class="item-amount text-right"><?php _trans('qty'); ?></th>
            <th class="item-price" style="text-align: center;"><?php _trans('price'); ?></th>
            <?php if ($show_item_discounts) : ?>
                <th class="item-discount text-right"><?php _trans('discount'); ?></th>
            <?php endif; ?>
            <th class="item-total" style="text-align: center;"><?php _trans('total'); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        $no=1;
        foreach ($items as $item) { ?>
            <tr>
                <td><?=$no++?></td>
                <td><?php _htmlsc($item->item_name); ?></td>
                <td class="text-right">
                    <?php echo format_amount($item->item_quantity); ?>
                    <?php if ($item->item_product_unit) : ?>
                        <br>
                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                    <?php endif; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($item->item_price); ?>
                </td>
                <?php if ($show_item_discounts) : ?>
                    <td class="text-right">
                        <?php echo format_currency($item->item_discount); ?>
                    </td>
                <?php endif; ?>
                <td class="text-right">
                    <?php echo format_currency($item->item_total); ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
        <tbody class="invoice-sums">

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <?php _trans('subtotal'); ?>
            </td>
            <td class="text-right"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
        </tr>

        <?php if ($invoice->invoice_item_tax_total > 0) { ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('item_tax'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice->invoice_item_tax_total); ?>
                </td>
            </tr>
        <?php } ?>

        <?php foreach ($invoice_tax_rates as $invoice_tax_rate) : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php echo htmlsc($invoice_tax_rate->invoice_tax_rate_name) ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                </td>
            </tr>
        <?php endforeach ?>

        <?php if ($invoice->invoice_discount_percent != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_amount($invoice->invoice_discount_percent); ?>%
                </td>
            </tr>
        <?php endif; ?>
        <?php if ($invoice->invoice_discount_amount != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice->invoice_discount_amount); ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <b><?php _trans('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($invoice->invoice_total); ?></b>
            </td>
        </tr>
        </tbody>
    </table>
    <footer>
        <div class="notes" style="font-family: Book Antiqua;">
        	<b style="font-family: Book Antiqua;"><?php _trans('terms'); ?></b><br/>
        	TRANSFER VIA BCA<br/>
        	A/C : 735-5333-345<br/>
        	A/N : LINAS MEDIA INFORMATIKA<br/>
        </div>
    </footer>
    <!-- <header class="clearfix">

    <div id="logo">
    	<table width="100%">
    		<tbody>
    			<tr>
    				<td style="padding: 5px;"><?php echo invoice_logo_pdf(); ?></td>
    				<td style="vertical-align: top; padding: 5px;">
    					<b><?=$invoice->user_name?></b>
    					<p><?=htmlsc($invoice->user_address_1)?> <?=htmlsc($invoice->user_address_2)?> <?=$invoice->user_city?></p>
    					<p>Phone: <?=htmlsc($invoice->user_phone)?>, Website: <?=$invoice->user_web?></p>
    				</td>
    			</tr>
    		</tbody>
    	</table>
    </div>

</header> -->


<!-- <main>

    <div width="100%" style="text-align: center;">
		<h3>INVOICE</h3>
	</div>
	<div>
        <table>
        	<tbody style="width: 100%">
        		<tr>
        			<td style="width: 30%;">Invoice No</td>
        			<td><?=$invoice->invoice_number?></td>
        		</tr>
        		<tr>
        			<td style="width: 30%;">Customer</td>
        			<td><?=json_encode($invoice)?></td>
        		</tr>
        		<tr>
        			<td style="width: 30%;">PIC Name</td>
        			<td><?=$invoice->invoice_number?></td>
        		</tr>
        		<tr>
        			<td style="width: 30%;">Alamat</td>
        			<td><?=htmlsc($invoice->client_address_1)." ".htmlsc($invoice->client_address_2)." ".htmlsc($invoice->client_city)?></td>
        		</tr>	
        	</tbody>
        </table>
    </div>

    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name"><?php _trans('item'); ?></th>
            <th class="item-desc"><?php _trans('description'); ?></th>
            <th class="item-amount text-right"><?php _trans('qty'); ?></th>
            <th class="item-price text-right"><?php _trans('price'); ?></th>
            <?php if ($show_item_discounts) : ?>
                <th class="item-discount text-right"><?php _trans('discount'); ?></th>
            <?php endif; ?>
            <th class="item-total text-right"><?php _trans('total'); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($items as $item) { ?>
            <tr>
                <td><?php _htmlsc($item->item_name); ?></td>
                <td><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                <td class="text-right">
                    <?php echo format_amount($item->item_quantity); ?>
                    <?php if ($item->item_product_unit) : ?>
                        <br>
                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                    <?php endif; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($item->item_price); ?>
                </td>
                <?php if ($show_item_discounts) : ?>
                    <td class="text-right">
                        <?php echo format_currency($item->item_discount); ?>
                    </td>
                <?php endif; ?>
                <td class="text-right">
                    <?php echo format_currency($item->item_total); ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
        <tbody class="invoice-sums">

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <?php _trans('subtotal'); ?>
            </td>
            <td class="text-right"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
        </tr>

        <?php if ($invoice->invoice_item_tax_total > 0) { ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('item_tax'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice->invoice_item_tax_total); ?>
                </td>
            </tr>
        <?php } ?>

        <?php foreach ($invoice_tax_rates as $invoice_tax_rate) : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php echo htmlsc($invoice_tax_rate->invoice_tax_rate_name) . ' (' . format_amount($invoice_tax_rate->invoice_tax_rate_percent) . '%)'; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                </td>
            </tr>
        <?php endforeach ?>

        <?php if ($invoice->invoice_discount_percent != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_amount($invoice->invoice_discount_percent); ?>%
                </td>
            </tr>
        <?php endif; ?>
        <?php if ($invoice->invoice_discount_amount != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice->invoice_discount_amount); ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <b><?php _trans('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($invoice->invoice_total); ?></b>
            </td>
        </tr>
        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <?php _trans('paid'); ?>
            </td>
            <td class="text-right">
                <?php echo format_currency($invoice->invoice_paid); ?>
            </td>
        </tr>
        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <b><?php _trans('balance'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($invoice->invoice_balance); ?></b>
            </td>
        </tr>
        </tbody>
    </table>

</main> -->

<!-- <footer>
    <?php if ($invoice->invoice_terms) : ?>
        <div class="notes">
            <b><?php _trans('terms'); ?></b><br/>
            <?php echo nl2br(htmlsc($invoice->invoice_terms)); ?>
        </div>
    <?php endif; ?>
</footer> -->

</body>
</html>
