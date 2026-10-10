<?php
/** Copy into the host adapter and replace the bricks-quicknav textdomain with its literal domain. */
defined( 'ABSPATH' ) || exit;
return static function ( string $message ): string {
    // Literal calls let the host's normal translation extractor collect every source string.
    switch ( $message ) {
        case 'Private mode must be boolean.':
            return __( 'Private mode must be boolean.', 'bricks-quicknav' );
        case 'Invalid authentication provider.':
            return __( 'Invalid authentication provider.', 'bricks-quicknav' );
        case 'The plugin must be installed in a stable slug directory.':
            return __( 'The plugin must be installed in a stable slug directory.', 'bricks-quicknav' );
        case 'Invalid GitHub repository URL.':
            return __( 'Invalid GitHub repository URL.', 'bricks-quicknav' );
        case 'The private update could not be authorized. Check the repository credentials and refresh updates.':
            return __( 'The private update could not be authorized. Check the repository credentials and refresh updates.', 'bricks-quicknav' );
        case 'Could not create the update download file.':
            return __( 'Could not create the update download file.', 'bricks-quicknav' );
        case 'The private update download failed. Check credentials and try again.':
            return __( 'The private update download failed. Check credentials and try again.', 'bricks-quicknav' );
        case 'Could not access the update filesystem.':
            return __( 'Could not access the update filesystem.', 'bricks-quicknav' );
        case 'GitHub release does not contain the plugin main file.':
            return __( 'GitHub release does not contain the plugin main file.', 'bricks-quicknav' );
        case 'Could not prepare the GitHub release package.':
            return __( 'Could not prepare the GitHub release package.', 'bricks-quicknav' );
        case 'See the release on GitHub.':
            return __( 'See the release on GitHub.', 'bricks-quicknav' );
        default:
            return $message;
    }
};
