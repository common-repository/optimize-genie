<?php
/**
 * Admin Page: OptimizeGenie Settings.
 * Tab: Configurations.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



use OptimizeGenie\Admin\Server;


?>
<style>
    .optimize-genie-admin-table tr, .optimize-genie-admin-table th, .optimize-genie-admin-table td {
        border: 1px solid #ccc;
        padding: 5px;
    }

    .optimize-genie-admin-wrapper {
        width: 100%;
    }
</style>
<?php
if ( Server::getServerType() === 'nginx' && ! Server::nginxConfigured() ) {
	?>
    <h1 class='warning'>OptimizeGenie is not configured properly!</h1>
    <div class='optimize-genie-admin-wrapper'>
        <h3>Steps to install OptimizeGenie on Nginx:</h3>
        <ol>
            <li>Open the nginx configuration file for your site <a
                        href='https://futrx.com/how-to-locate-nginx-configuration-file'>Guide on how to locate your
                    nginx
                    configuration file</a></li>
            <li>Add the following code below the <code>server_name ...;</code> line:<br>
                <p>
                    <code>include <?php echo esc_attr( trim( Server::getDocumentRoot() ) ); ?>
                        /wp-content/plugins/optimize-genie/customizer/nginx.conf;
                        #Managed by Optimize Genie</code>
                </p>
                <p>
                    <img src="<?php echo esc_url( OptimizeGenie_Plugin_Url . 'assets/server-screen-shot.png' ); ?>"
                         width='700'/>
                </p>

            </li>
            <li>Restart your nginx server. More details here: <a href="https://futrx.com/how-to-restart-nginx-server">Guide
                    on how to restart nginx server</a></li>
        </ol>

    </div>
	<?php
} else {
	?>
    <h3><?php echo esc_attr__( 'Everything looks good, nothing to do here!', 'optimize-genie' ) ?></h3>
	<?php
}
?>


