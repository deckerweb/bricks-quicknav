<?php
/** Escaped categorized release history. @package BricksQuickNav */
defined( 'ABSPATH' ) || exit;

/**
 * Render local history shared with the generated documentation.
 * @param bool $german Whether to use German labels and text.
 * @return string Escaped structured HTML, suitable for dialogs and plugin information.
 */
return static function ( $german ) {
	$data = json_decode( (string) file_get_contents( __DIR__ . '/history.json' ), true );
	if ( ! is_array( $data ) ) { return ''; }
	$language = $german ? 'de' : 'en';
	$labels = $german ? array( 'new' => 'Neu', 'improved' => 'Verbessert', 'fixed' => 'Behoben', 'misc' => 'Sonstiges' ) : array( 'new' => 'New', 'improved' => 'Improved', 'fixed' => 'Fixed', 'misc' => 'Misc' );
	$html = '<div class="bxqn-history">';
	foreach ( $data as $release ) {
		$html .= '<section><h3>' . esc_html( $release['version'] ) . '</h3><p>' . esc_html( $release['date'][ $language ] ) . '</p><ul>';
		foreach ( $release['items'] as $item ) { $html .= '<li><span class="bxqn-history-badge bxqn-history-' . esc_attr( in_array( $item['category'], array_keys( $labels ), true ) ? $item['category'] : 'misc' ) . '">' . esc_html( $labels[ $item['category'] ] ?? $labels['misc'] ) . ':</span> ' . esc_html( $item[ $language ] ) . '</li>'; }
		$html .= '</ul></section>';
	}
	return $html . '</div>';
};
