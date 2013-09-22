<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<table cellspacing="0" width="100%" class="list-data">
<thead>
<tr class="italic">
    <th width="5%">No.</th>
    <th width="10%">Transaksi</th>
    <th width="10%">Waktu</th>
    <th width="30%">Nama Barang</th>
<!--    <th width="10%">No. Batch</th>
    <th width="10%">ED</th>-->
    <th width="10%">Awal</th>
    <th width="10%">Masuk</th>
    <th width="10%">Keluar</th>
    <th width="10%">Sisa</th>
    <!--<th width="4%">#</th>-->
</tr>
</thead>
<tbody>
    <?php 
    $limit = 10;
    $page  = $_GET['page'];
    if ($_GET['page'] === '') {
        $page = 1;
        $offset = 0;
    } else {
        $offset = ($page-1)*$limit;
    }
    
    $param = array(
        'id' => $_GET['id'],
        'limit' => $limit,
        'start' => $offset,
        'awal' => date2mysql($_GET['awal']),
        'akhir' => date2mysql($_GET['akhir']),
        'perundangan' => $_GET['perundangan']
    );
    $list_data = load_data_arus_stok($param);
    $master_arus_stok = $list_data['data'];
    $total_data = $list_data['total'];
    foreach ($master_arus_stok as $key => $data) { 
        $awalnya = mysql_fetch_object(mysql_query("select (sum(masuk)-sum(keluar)) as awal from stok where transaksi != 'Pemesanan' and waktu < '".$data->waktu."' and id_barang = '".$data->id_barang."'")); // ngarah gampang
        $sisanya = mysql_fetch_object(mysql_query("select (sum(masuk)-sum(keluar)) as sisa from stok where transaksi != 'Pemesanan' and waktu <= '".$data->waktu."' and id_barang = '".$data->id_barang."'"));  // ngarah gampang
        ?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= (++$key+$offset) ?></td>
        <td><?= $data->transaksi ?></td>
        <td align="center"><?= datetimefmysql($data->waktu) ?></td>
        <td><?= $data->nama.' '.$data->kekuatan.' '.$data->satuan_kekuatan ?></td>
<!--        <td><?= $data->nobatch ?></td>
        <td align="center"><?= datefmysql($data->ed) ?></td>-->
        <td align="center"><?= isset($awalnya->awal)?$awalnya->awal:'0' ?></td>
        <td align="center"><?= $data->masuk ?></td>
        <td align="center"><?= $data->keluar ?></td>
        <td align="center"><?= $sisanya->sisa ?></td>
        <!--<td class='aksi' align='center'>
            <a class='edition' onclick="edit_stokopname('<?= $str ?>');" title="Klik untuk edit stok_opname">&nbsp;</a>
            <a class='deletion' onclick="delete_stokopname('<?= $data->id ?>', '<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
        </td>-->
    </tr>
    <?php } ?>
</tbody>
</table>
<?= paging_ajax($total_data, $limit, $page, '1', generate_get_parameter($_GET)) ?>