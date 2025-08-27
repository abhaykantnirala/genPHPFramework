<?php if (!is_object($_elsinfo_)) { ?>
    <h2>Elastic Search DB Connection Failed</h2>
    <?php die;
}
?>
<h2>ELS Control System</h2>
<div class="row">
    <div class="col-md-3">
        <table>
            <tr>
                <td colspan="2" style="text-align: center; color: blue; font-size: 16px;">Elastic Search Server Information</td>
            </tr>
            <tr>
                <td>Server</td>
                <td><?php echo $_elsinfo_->name; ?></td>
            </tr>
            <tr>
                <td>Cluster Name</td>
                <td><?php echo $_elsinfo_->cluster_name; ?></td>
            </tr>
            <tr>
                <td>Cluster uuid</td>
                <td><?php echo $_elsinfo_->cluster_uuid; ?></td>
            </tr>
            <tr>
                <td>Elastic Search Version</td>
                <td><?php echo $_elsinfo_->version->number; ?></td>
            </tr>
            <tr>
                <td>Lucene Version</td>
                <td><?php echo $_elsinfo_->version->lucene_version; ?></td>
            </tr>
            <tr>
                <td>Old Version compatibility</td>
                <td><?php echo $_elsinfo_->version->minimum_wire_compatibility_version; ?></td>
            </tr>
        </table>
    </div>
</div>