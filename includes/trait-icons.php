<?php
/** Preserved Bricks QuickNav navigation definitions. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Defines Icons navigation without changing editor layout. */
trait Icons {
	/**
	 * Get SVG code for specified icon.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $icon  Key for specified icon type.
	 * @return string $svg   SVG code for icon.
	 */
	private function get_icon( $icon ) {
		
		/** Check icon type */
		switch ( sanitize_key( $icon ) ) {
			case 'bricks-b':
				$svg = '<span class="icon-svg ab-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><path d="M7.94514768,0 L8.35021097,0.253164557 L8.35021097,7.29113924 C9.77919139,6.34598684 11.3600476,5.87341772 13.092827,5.87341772 C15.5907298,5.87341772 17.6610326,6.74542025 19.3037975,8.48945148 C20.9240587,10.2334827 21.7341772,12.382547 21.7341772,14.9367089 C21.7341772,17.5021225 20.9184329,19.6511868 19.2869198,21.3839662 C17.6441549,23.1279975 15.579478,24 13.092827,24 C10.9212268,24 9.06470532,23.2236365 7.52320675,21.6708861 L7.52320675,23.5780591 L3,23.5780591 L3,0.556962025 L7.94514768,0 Z M12.2320675,10.4472574 C11.0393752,10.4472574 10.0436046,10.8523166 9.24472574,11.6624473 C8.44584692,12.4950815 8.0464135,13.5864911 8.0464135,14.9367089 C8.0464135,16.2869266 8.44584692,17.3727104 9.24472574,18.1940928 C10.0323527,19.0154753 11.0281234,19.4261603 12.2320675,19.4261603 C13.5035225,19.4261603 14.5330481,18.9985978 15.3206751,18.1434599 C16.0970503,17.2995738 16.4852321,16.2306675 16.4852321,14.9367089 C16.4852321,13.6427502 16.0914245,12.5682181 15.3037975,11.7130802 C14.5161705,10.8691941 13.4922707,10.4472574 12.2320675,10.4472574 Z" fill="currentcolor" fill-rule="nonzero"></path></g></svg></span> ';
				break;
			case 'bricks-yellow':
				$svg = '<span class="icon-svg ab-icon"><svg clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 772 772" xmlns="http://www.w3.org/2000/svg"><path d="m0 0h772v772h-772z" fill="#ffd53e"/><path d="m0 0h772v772h-772z" fill="none"/><path d="m25.188 11.344.75.469v13.031c2.645-1.75 5.572-2.625 8.781-2.625 4.625 0 8.458 1.614 11.5 4.844 3 3.229 4.5 7.208 4.5 11.937 0 4.75-1.511 8.729-4.531 11.938-3.042 3.229-6.865 4.843-11.469 4.843-4.021 0-7.459-1.437-10.313-4.312v3.531h-8.375v-42.625zm7.937 19.344c-2.208 0-4.052.749-5.531 2.25-1.479 1.541-2.219 3.562-2.219 6.062s.74 4.51 2.219 6.031c1.458 1.521 3.302 2.282 5.531 2.282 2.354 0 4.26-.792 5.719-2.375 1.437-1.563 2.156-3.542 2.156-5.938s-.729-4.385-2.187-5.969c-1.459-1.562-3.355-2.343-5.688-2.343z" fill="#212121" fill-rule="nonzero" transform="matrix(11.2518 0 0 11.2518 10.4726 8.36287)"/></svg></span> ';
				break;
			case 'bricks-brick':
				$svg = '<span class="icon-svg ab-icon"><svg height="80" viewBox="0 0 80 80" width="80" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><path d="m40 40h40v40h-40z" fill="#fe9"/><path d="m0 40h40v40h-40z" fill="#ffd53e"/><path d="m0 0h40v40h-40z" fill="#ffe66d"/></g></svg></span> ';
				break;
			case 'add':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5H13V11H19V13H13V19H11V13H5V11H11Z"></path></svg></span> ';
				break;
			case 'page':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M20 22H4C3.44772 22 3 21.5523 3 21V3C3 2.44772 3.44772 2 4 2H20C20.5523 2 21 2.44772 21 3V21C21 21.5523 20.5523 22 20 22ZM19 20V4H5V20H19ZM7 6H11V10H7V6ZM7 12H17V14H7V12ZM7 16H17V18H7V16ZM13 7H17V9H13V7Z"></path></svg></span> ';
				break;
			case 'template':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5 8V20H19V8H5ZM5 6H19V4H5V6ZM20 22H4C3.44772 22 3 21.5523 3 21V3C3 2.44772 3.44772 2 4 2H20C20.5523 2 21 2.44772 21 3V21C21 21.5523 20.5523 22 20 22ZM7 10H11V14H7V10ZM7 16H17V18H7V16ZM13 11H17V13H13V11Z"></path></svg></span> ';
				break;
			case 'filter':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M10 18H14V16H10V18ZM3 6V8H21V6H3ZM6 13H18V11H6V13Z"></path></svg></span> ';
				break;
			case 'bundle':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 10H2V4.00293C2 3.44903 2.45531 3 2.9918 3H21.0082C21.556 3 22 3.43788 22 4.00293V10H21V20.0015C21 20.553 20.5551 21 20.0066 21H3.9934C3.44476 21 3 20.5525 3 20.0015V10ZM19 10H5V19H19V10ZM4 5V8H20V5H4ZM9 12H15V14H9V12Z"></path></svg></span> ';
				break;
			case 'tag':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M10.9042 2.10025L20.8037 3.51446L22.2179 13.414L13.0255 22.6063C12.635 22.9969 12.0019 22.9969 11.6113 22.6063L1.71184 12.7069C1.32131 12.3163 1.32131 11.6832 1.71184 11.2926L10.9042 2.10025ZM11.6113 4.22157L3.83316 11.9997L12.3184 20.485L20.0966 12.7069L19.036 5.28223L11.6113 4.22157ZM13.7327 10.5855C12.9516 9.80448 12.9516 8.53815 13.7327 7.7571C14.5137 6.97606 15.78 6.97606 16.5611 7.7571C17.3421 8.53815 17.3421 9.80448 16.5611 10.5855C15.78 11.3666 14.5137 11.3666 13.7327 10.5855Z"></path></svg></span> ';
				break;
			case 'code':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12L18.3431 17.6569L16.9289 16.2426L21.1716 12L16.9289 7.75736L18.3431 6.34315L24 12ZM2.82843 12L7.07107 16.2426L5.65685 17.6569L0 12L5.65685 6.34315L7.07107 7.75736L2.82843 12ZM9.78845 21H7.66009L14.2116 3H16.3399L9.78845 21Z"></path></svg></span> ';
				break;
			case 'snippets':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M4 18V14.3C4 13.4716 3.32843 12.8 2.5 12.8H2V11.2H2.5C3.32843 11.2 4 10.5284 4 9.7V6C4 4.34315 5.34315 3 7 3H8V5H7C6.44772 5 6 5.44772 6 6V10.1C6 10.9858 5.42408 11.7372 4.62623 12C5.42408 12.2628 6 13.0142 6 13.9V18C6 18.5523 6.44772 19 7 19H8V21H7C5.34315 21 4 19.6569 4 18ZM20 14.3V18C20 19.6569 18.6569 21 17 21H16V19H17C17.5523 19 18 18.5523 18 18V13.9C18 13.0142 18.5759 12.2628 19.3738 12C18.5759 11.7372 18 10.9858 18 10.1V6C18 5.44772 17.5523 5 17 5H16V3H17C18.6569 3 20 4.34315 20 6V9.7C20 10.5284 20.6716 11.2 21.5 11.2H22V12.8H21.5C20.6716 12.8 20 13.4716 20 14.3Z"></path></svg></span> ';
				break;
			case 'css3':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M2.8 14H4.83961L4.2947 16.7245L10.0393 18.8787L17.2665 16.4697L18.3604 11H3.4L3.8 9H18.7604L19.5604 5H4.6L5 3H22L19 18L10 21L2 18L2.8 14Z"></path></svg></span> ';
				break;
			case 'cf':
				$svg = '<span class="icon-svg"><svg width="18" height="14" viewBox="0 0 18 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M17.4353 5.86029H10.2935V9.73973H17.4353V5.86029Z"/><path d="M6.80752 0C3.04749 0 0 3.05087 0 6.81508C0 10.5793 3.04749 13.6302 6.80752 13.6302H10.299V9.75072H6.80752C5.1906 9.75072 3.87513 8.43928 3.87513 6.81508C3.87513 5.19636 5.18512 3.87944 6.80752 3.87944H17.4408V0H6.80752Z"/></svg></span> ';
				break;
			case 'acss':
				$svg = '<span class="icon-svg"><svg width="50" height="38" viewBox="0 0 50 38" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M50 25.8533L10.404 28.8145L21.9245 8.85699L31.4833 25.412L36.3849 25.0452L21.9245 0L0 37.9745L33.9819 29.7395L36.034 33.3121L17.5811 37.9745H43.849L38.4689 28.655L50 25.8533Z"/></svg></span> ';
				break;
			case 'frames':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M21 20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V4C3 3.44772 3.44772 3 4 3H20C20.5523 3 21 3.44772 21 4V20ZM11 5H5V19H11V5ZM19 13H13V19H19V13ZM19 5H13V11H19V5Z"></path></svg></span> ';
				break;
			case 'windpress':
				$svg = '<span class="icon-svg"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" fill="currentColor" xml:space="preserve"><g><path fill="currentColor" d="M176,384H16c-8.832,0-16,7.168-16,16c0,8.832,7.168,16,16,16h160c8.832,0,16,7.2,16,16s-7.168,16-16,16 c-8.832,0-16,7.168-16,16c0,8.832,7.168,16,16,16c26.464,0,48-21.536,48-48S202.464,384,176,384z" /></g><g><path d="M240,256c-8.832,0-16,7.168-16,16c0,8.832,7.168,16,16,16c8.832,0,16,7.2,16,16s-7.168,16-16,16H16 c-8.832,0-16,7.168-16,16c0,8.832,7.168,16,16,16h224c26.464,0,48-21.536,48-48S266.464,256,240,256z" /></g><g><path d="M288,32C164.288,32,64,132.288,64,256c0,10.88,1.056,21.536,2.56,32h128.192c-1.792-4.992-2.752-10.4-2.752-16 c0-26.464,21.536-48,48-48c44.096,0,80,35.904,80,80c0,44.128-35.904,80-80,80h-0.416C249.76,397.408,256,413.92,256,432 c0,16.032-4.864,30.944-13.024,43.456c14.56,2.976,29.6,4.544,45.024,4.544c123.712,0,224-100.288,224-224S411.712,32,288,32z" /></g></svg></span> ';
				break;
			case 'warning':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20ZM11 15H13V17H11V15ZM11 7H13V13H11V7Z"></path></svg></span> ';
				break;
			case 'inbox':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M4.02381 3.78307C4.12549 3.32553 4.5313 3 5 3H19C19.4687 3 19.8745 3.32553 19.9762 3.78307L21.9762 12.7831C21.992 12.8543 22 12.927 22 13V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V13C2 12.927 2.00799 12.8543 2.02381 12.7831L4.02381 3.78307ZM5.80217 5L4.24662 12H9C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12H19.7534L18.1978 5H5.80217ZM16.584 14C15.8124 15.7659 14.0503 17 12 17C9.94968 17 8.1876 15.7659 7.41604 14H4V19H20V14H16.584Z"></path></svg></span> ';
				break;
			case 'font':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M10 6V21H8V6H2V4H16V6H10ZM18 14V21H16V14H13V12H21V14H18Z"></path></svg></span> ';
				break;
			case 'sidebars':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3H21C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3ZM9 5V19H20V5H9Z"></path></svg></span> ';
				break;
			case 'settings':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5.32943 3.27158C6.56252 2.8332 7.9923 3.10749 8.97927 4.09446C10.1002 5.21537 10.3019 6.90741 9.5843 8.23385L20.293 18.9437L18.8788 20.3579L8.16982 9.64875C6.84325 10.3669 5.15069 10.1654 4.02952 9.04421C3.04227 8.05696 2.7681 6.62665 3.20701 5.39332L5.44373 7.63C6.02952 8.21578 6.97927 8.21578 7.56505 7.63C8.15084 7.04421 8.15084 6.09446 7.56505 5.50868L5.32943 3.27158ZM15.6968 5.15512L18.8788 3.38736L20.293 4.80157L18.5252 7.98355L16.7574 8.3371L14.6361 10.4584L13.2219 9.04421L15.3432 6.92289L15.6968 5.15512ZM8.97927 13.2868L10.3935 14.7011L5.09018 20.0044C4.69966 20.3949 4.06649 20.3949 3.67597 20.0044C3.31334 19.6417 3.28744 19.0699 3.59826 18.6774L3.67597 18.5902L8.97927 13.2868Z"></path></svg></span> ';
				break;
			case 'info':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20ZM11 7H13V9H11V7ZM11 11H13V17H11V11Z"></path></svg></span> ';
				break;
			case 'gear':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L21.5 6.5V17.5L12 23L2.5 17.5V6.5L12 1ZM12 3.311L4.5 7.65311V16.3469L12 20.689L19.5 16.3469V7.65311L12 3.311ZM12 16C9.79086 16 8 14.2091 8 12C8 9.79086 9.79086 8 12 8C14.2091 8 16 9.79086 16 12C16 14.2091 14.2091 16 12 16ZM12 14C13.1046 14 14 13.1046 14 12C14 10.8954 13.1046 10 12 10C10.8954 10 10 10.8954 10 12C10 13.1046 10.8954 14 12 14Z"></path></svg></span> ';
				break;
			case 'plugin':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M13 18V20H19V22H13C11.8954 22 11 21.1046 11 20V18H8C5.79086 18 4 16.2091 4 14V7C4 6.44772 4.44772 6 5 6H8V2H10V6H14V2H16V6H19C19.5523 6 20 6.44772 20 7V14C20 16.2091 18.2091 18 16 18H13ZM8 16H16C17.1046 16 18 15.1046 18 14V11H6V14C6 15.1046 6.89543 16 8 16ZM18 8H6V9H18V8ZM12 14.5C11.4477 14.5 11 14.0523 11 13.5C11 12.9477 11.4477 12.5 12 12.5C12.5523 12.5 13 12.9477 13 13.5C13 14.0523 12.5523 14.5 12 14.5Z"></path></svg></span> ';
				break;
			case 'learn':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M8 4C8 5.10457 7.10457 6 6 6 4.89543 6 4 5.10457 4 4 4 2.89543 4.89543 2 6 2 7.10457 2 8 2.89543 8 4ZM5 16V22H3V10C3 8.34315 4.34315 7 6 7 6.82059 7 7.56423 7.32946 8.10585 7.86333L10.4803 10.1057 12.7931 7.79289 14.2073 9.20711 10.5201 12.8943 9 11.4587V22H7V16H5ZM10 5H19V14H10V16H14.3654L17.1889 22H19.3993L16.5758 16H20C20.5523 16 21 15.5523 21 15V4C21 3.44772 20.5523 3 20 3H10V5Z"></path></svg></span> ';
				break;
			case 'collection':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M13.0607 8.11097L14.4749 9.52518C17.2086 12.2589 17.2086 16.691 14.4749 19.4247L14.1214 19.7782C11.3877 22.5119 6.95555 22.5119 4.22188 19.7782C1.48821 17.0446 1.48821 12.6124 4.22188 9.87874L5.6361 11.293C3.68348 13.2456 3.68348 16.4114 5.6361 18.364C7.58872 20.3166 10.7545 20.3166 12.7072 18.364L13.0607 18.0105C15.0133 16.0578 15.0133 12.892 13.0607 10.9394L11.6465 9.52518L13.0607 8.11097ZM19.7782 14.1214L18.364 12.7072C20.3166 10.7545 20.3166 7.58872 18.364 5.6361C16.4114 3.68348 13.2456 3.68348 11.293 5.6361L10.9394 5.98965C8.98678 7.94227 8.98678 11.1081 10.9394 13.0607L12.3536 14.4749L10.9394 15.8891L9.52518 14.4749C6.79151 11.7413 6.79151 7.30911 9.52518 4.57544L9.87874 4.22188C12.6124 1.48821 17.0446 1.48821 19.7782 4.22188C22.5119 6.95555 22.5119 11.3877 19.7782 14.1214Z"></path></svg></span> ';
				break;
			case 'links':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M10 6V8H5V19H16V14H18V20C18 20.5523 17.5523 21 17 21H4C3.44772 21 3 20.5523 3 20V7C3 6.44772 3.44772 6 4 6H10ZM21 3V11H19L18.9999 6.413L11.2071 14.2071L9.79289 12.7929L17.5849 5H13V3H21Z"></path></svg></span> ';
				break;
			case 'community':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17 6C16.4477 6 16 5.55228 16 5C16 4.44772 16.4477 4 17 4C17.5523 4 18 4.44772 18 5C18 5.55228 17.5523 6 17 6ZM17 8C18.6569 8 20 6.65685 20 5C20 3.34315 18.6569 2 17 2C15.3431 2 14 3.34315 14 5C14 6.65685 15.3431 8 17 8ZM7 3C4.79086 3 3 4.79086 3 7V9H5V7C5 5.89543 5.89543 5 7 5H10V3H7ZM17 21C19.2091 21 21 19.2091 21 17V15H19V17C19 18.1046 18.1046 19 17 19H14V21H17ZM8 13C8 12.4477 7.55228 12 7 12C6.44772 12 6 12.4477 6 13C6 13.5523 6.44772 14 7 14C7.55228 14 8 13.5523 8 13ZM10 13C10 14.6569 8.65685 16 7 16C5.34315 16 4 14.6569 4 13C4 11.3431 5.34315 10 7 10C8.65685 10 10 11.3431 10 13ZM17 11C15.8954 11 15 11.8954 15 13H13C13 10.7909 14.7909 9 17 9C19.2091 9 21 10.7909 21 13H19C19 11.8954 18.1046 11 17 11ZM5 21C5 19.8954 5.89543 19 7 19C8.10457 19 9 19.8954 9 21H11C11 18.7909 9.20914 17 7 17C4.79086 17 3 18.7909 3 21H5Z"></path></svg></span> ';
				break;
			case 'about':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17.841 15.659L18.017 15.836L18.1945 15.659C19.0732 14.7803 20.4978 14.7803 21.3765 15.659C22.2552 16.5377 22.2552 17.9623 21.3765 18.841L18.0178 22.1997L14.659 18.841C13.7803 17.9623 13.7803 16.5377 14.659 15.659C15.5377 14.7803 16.9623 14.7803 17.841 15.659ZM12 14V16C8.68629 16 6 18.6863 6 22H4C4 17.6651 7.44784 14.1355 11.7508 14.0038L12 14ZM12 1C15.315 1 18 3.685 18 7C18 10.2397 15.4357 12.8776 12.225 12.9959L12 13C8.685 13 6 10.315 6 7C6 3.76034 8.56434 1.12237 11.775 1.00414L12 1ZM12 3C9.78957 3 8 4.78957 8 7C8 9.21043 9.78957 11 12 11C14.2104 11 16 9.21043 16 7C16 4.78957 14.2104 3 12 3Z"></path></svg></span> ';
				break;
			default:
				$svg = '';
		}  // end switch
		
		return $svg;
	}

	/**
	 * Get node data for Title & Meta; set "link" icon for external links.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $url     URL of the admin page or external link.
	 * @param  string $label   Label for title of the node.
	 * @return array  $output  Array containing meta & title parameters of node.
	 */
	private function get_node_data( $url, $label = '' ) {
		
		$output = [];
		$output[ 'meta' ]  = ( strpos( $url, '.php' ) !== FALSE ) ? [] : [ 'class' => 'has-icon', 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ];
		$output[ 'title' ] = ( strpos( $url, '.php' ) !== FALSE ) ? esc_html( $label ) : $this->get_icon( 'links' ) . esc_html( $label );
		
		return $output;
	}
}
