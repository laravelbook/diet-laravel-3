<?php
/**
 * @package  Laravel 3 Lite
 * @version  3.2.13
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 * @link     http://laravel.com 
 * @author   Max Ehsan <contact@laravelbook.com>
 * @link     http://laravelbook.com/
 * 
 * Highly condensed and optimized version of Laravel 3 framework.
 * 
 * This file is the result of merging commonly used Laravel and Symfony 
 * class files with some extraneous components and comments stripped away.
 *
 * By using this file instead of laravel.php, an Laravel application may
 * improve performance due to the reduction of PHP parsing time.
 * The performance improvement will become especially obvious when PHP op-code 
 * caching engine such as the APC extension is enabled.
 *
 * DO NOT MODIFY THIS FILE MANUALLY!
 *
 */
namespace Symfony\Component\HttpFoundation\Session\Attribute {
	use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface.php
	 */
	interface AttributeBagInterface extends SessionBagInterface {
		public function has($name);
		public function get($name, $default = null);
		public function set($name, $value);
		public function all();
		public function replace(array $attributes);
		public function remove($name);
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag.php
	 */
	class AttributeBag implements AttributeBagInterface, \IteratorAggregate, \Countable
	{
		private $name = 'attributes';
		private $storageKey;
		protected $attributes = array();
		public function __construct($storageKey = '_sf2_attributes')
		{
			$this->storageKey = $storageKey;
		}
		public function getName()
		{
			return $this->name;
		}
		public function setName($name)
		{
			$this->name = $name;
		}
		public function initialize(array & $attributes)
		{
			$this->attributes = & $attributes;
		}
		public function getStorageKey()
		{
			return $this->storageKey;
		}
		public function has($name)
		{
			return array_key_exists($name, $this->attributes);
		}
		public function get($name, $default = null)
		{
			return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
		}
		public function set($name, $value)
		{
			$this->attributes[$name] = $value;
		}
		public function all()
		{
			return $this->attributes;
		}
		public function replace(array $attributes)
		{
			$this->attributes = array();
			foreach ($attributes as $key => $value) {
				$this->set($key, $value);
			}
		}
		public function remove($name)
		{
			$retval = null;
			if (array_key_exists($name, $this->attributes)) {
				$retval = $this->attributes[$name];
				unset($this->attributes[$name]);
			}
			return $retval;
		}
		public function clear()
		{
			$return = $this->attributes;
			$this->attributes = array();
			return $return;
		}
		public function getIterator()
		{
			return new \ArrayIterator($this->attributes);
		}
		public function count()
		{
			return count($this->attributes);
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag.php
	 */
	class NamespacedAttributeBag extends AttributeBag
	{
		private $namespaceCharacter;
		public function __construct($storageKey = '_sf2_attributes', $namespaceCharacter = '/')
		{
			$this->namespaceCharacter = $namespaceCharacter;
			parent::__construct($storageKey);
		}
		public function has($name)
		{
			$attributes = $this->resolveAttributePath($name);
			$name = $this->resolveKey($name);
			return array_key_exists($name, $attributes);
		}
		public function get($name, $default = null)
		{
			$attributes = $this->resolveAttributePath($name);
			$name = $this->resolveKey($name);
			return array_key_exists($name, $attributes) ? $attributes[$name] : $default;
		}
		public function set($name, $value)
		{
			$attributes = & $this->resolveAttributePath($name, true);
			$name = $this->resolveKey($name);
			$attributes[$name] = $value;
		}
		public function remove($name)
		{
			$retval = null;
			$attributes = & $this->resolveAttributePath($name);
			$name = $this->resolveKey($name);
			if (array_key_exists($name, $attributes)) {
				$retval = $attributes[$name];
				unset($attributes[$name]);
			}
			return $retval;
		}
		protected function &resolveAttributePath($name, $writeContext = false)
		{
			$array = & $this->attributes;
			$name = (strpos($name, $this->namespaceCharacter) === 0) ? substr($name, 1) : $name;
			if (!$name) {
				return $array;
			}
			$parts = explode($this->namespaceCharacter, $name);
			if (count($parts) < 2) {
				if (!$writeContext) {
					return $array;
				}
				$array[$parts[0]] = array();
				return $array;
			}
			unset($parts[count($parts) - 1]);
			foreach ($parts as $part) {
				if (!array_key_exists($part, $array)) {
					if (!$writeContext) {
						return $array;
					}
					$array[$part] = array();
				}
				$array = & $array[$part];
			}
			return $array;
		}
		protected function resolveKey($name)
		{
			if (strpos($name, $this->namespaceCharacter) !== false) {
				$name = substr($name, strrpos($name, $this->namespaceCharacter) + 1, strlen($name));
			}
			return $name;
		}
	}
}
namespace Symfony\Component\HttpFoundation\File\MimeType
{
	use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
	use Symfony\Component\HttpFoundation\File\Exception\FileException;
	use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser.php
	 */
	class ExtensionGuesser implements ExtensionGuesserInterface
	{
		private static $instance = null;
		protected $guessers = array();
		public static function getInstance()
		{
			if (null === self::$instance) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		private function __construct()
		{
			$this->register(new MimeTypeExtensionGuesser());
		}
		public function register(ExtensionGuesserInterface $guesser)
		{
			array_unshift($this->guessers, $guesser);
		}
		public function guess($mimeType)
		{
			foreach ($this->guessers as $guesser) {
				$extension = $guesser->guess($mimeType);
				if (null !== $extension) {
					break;
				}
			}
			return $extension;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface.php
	 */
	interface ExtensionGuesserInterface
	{
		public function guess($mimeType);
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\MimeType\FileBinaryMimeTypeGuesser.php
	 */
	class FileBinaryMimeTypeGuesser implements MimeTypeGuesserInterface
	{
		private $cmd;
		public function __construct($cmd = 'file -b --mime %s 2>/dev/null')
		{
			$this->cmd = $cmd;
		}
		public static function isSupported()
		{
			return !defined('PHP_WINDOWS_VERSION_BUILD');
		}
		public function guess($path)
		{
			if (!is_file($path)) {
				throw new FileNotFoundException($path);
			}
			if (!is_readable($path)) {
				throw new AccessDeniedException($path);
			}
			if (!self::isSupported()) {
				return null;
			}
			ob_start();
			passthru(sprintf($this->cmd, escapeshellarg($path)), $return);
			if ($return > 0) {
				ob_end_clean();
				return null;
			}
			$type = trim(ob_get_clean());
			if (!preg_match('#^([a-z0-9\-]+/[a-z0-9\-]+)#i', $type, $match)) {
				return null;
			}
			return $match[1];
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser.php
	 */
	class FileinfoMimeTypeGuesser implements MimeTypeGuesserInterface
	{
		public static function isSupported()
		{
			return function_exists('finfo_open');
		}
		public function guess($path)
		{
			if (!is_file($path)) {
				throw new FileNotFoundException($path);
			}
			if (!is_readable($path)) {
				throw new AccessDeniedException($path);
			}
			if (!self::isSupported()) {
				return null;
			}
			if (!$finfo = new \finfo(FILEINFO_MIME_TYPE)) {
				return null;
			}
			return $finfo->file($path);
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser.php
	 */
	class MimeTypeExtensionGuesser implements ExtensionGuesserInterface
	{
		protected $defaultExtensions = array('application/andrew-inset' => 'ez', 'application/applixware' => 'aw', 'application/atom+xml' => 'atom', 'application/atomcat+xml' => 'atomcat', 'application/atomsvc+xml' => 'atomsvc', 'application/ccxml+xml' => 'ccxml', 'application/cdmi-capability' => 'cdmia', 'application/cdmi-container' => 'cdmic', 'application/cdmi-domain' => 'cdmid', 'application/cdmi-object' => 'cdmio', 'application/cdmi-queue' => 'cdmiq', 'application/cu-seeme' => 'cu', 'application/davmount+xml' => 'davmount', 'application/dssc+der' => 'dssc', 'application/dssc+xml' => 'xdssc', 'application/ecmascript' => 'ecma', 'application/emma+xml' => 'emma', 'application/epub+zip' => 'epub', 'application/exi' => 'exi', 'application/font-tdpfr' => 'pfr', 'application/hyperstudio' => 'stk', 'application/inkml+xml' => 'ink', 'application/ipfix' => 'ipfix', 'application/java-archive' => 'jar', 'application/java-serialized-object' => 'ser', 'application/java-vm' => 'class', 'application/javascript' => 'js', 'application/json' => 'json', 'application/lost+xml' => 'lostxml', 'application/mac-binhex40' => 'hqx', 'application/mac-compactpro' => 'cpt', 'application/mads+xml' => 'mads', 'application/marc' => 'mrc', 'application/marcxml+xml' => 'mrcx', 'application/mathematica' => 'ma', 'application/mathml+xml' => 'mathml', 'application/mbox' => 'mbox', 'application/mediaservercontrol+xml' => 'mscml', 'application/metalink4+xml' => 'meta4', 'application/mets+xml' => 'mets', 'application/mods+xml' => 'mods', 'application/mp21' => 'm21', 'application/mp4' => 'mp4s', 'application/msword' => 'doc', 'application/mxf' => 'mxf', 'application/octet-stream' => 'bin', 'application/oda' => 'oda', 'application/oebps-package+xml' => 'opf', 'application/ogg' => 'ogx', 'application/onenote' => 'onetoc', 'application/oxps' => 'oxps', 'application/patch-ops-error+xml' => 'xer', 'application/pdf' => 'pdf', 'application/pgp-encrypted' => 'pgp', 'application/pgp-signature' => 'asc', 'application/pics-rules' => 'prf', 'application/pkcs10' => 'p10', 'application/pkcs7-mime' => 'p7m', 'application/pkcs7-signature' => 'p7s', 'application/pkcs8' => 'p8', 'application/pkix-attr-cert' => 'ac', 'application/pkix-cert' => 'cer', 'application/pkix-crl' => 'crl', 'application/pkix-pkipath' => 'pkipath', 'application/pkixcmp' => 'pki', 'application/pls+xml' => 'pls', 'application/postscript' => 'ai', 'application/prs.cww' => 'cww', 'application/pskc+xml' => 'pskcxml', 'application/rdf+xml' => 'rdf', 'application/reginfo+xml' => 'rif', 'application/relax-ng-compact-syntax' => 'rnc', 'application/resource-lists+xml' => 'rl', 'application/resource-lists-diff+xml' => 'rld', 'application/rls-services+xml' => 'rs', 'application/rpki-ghostbusters' => 'gbr', 'application/rpki-manifest' => 'mft', 'application/rpki-roa' => 'roa', 'application/rsd+xml' => 'rsd', 'application/rss+xml' => 'rss', 'application/rtf' => 'rtf', 'application/sbml+xml' => 'sbml', 'application/scvp-cv-request' => 'scq', 'application/scvp-cv-response' => 'scs', 'application/scvp-vp-request' => 'spq', 'application/scvp-vp-response' => 'spp', 'application/sdp' => 'sdp', 'application/set-payment-initiation' => 'setpay', 'application/set-registration-initiation' => 'setreg', 'application/shf+xml' => 'shf', 'application/smil+xml' => 'smi', 'application/sparql-query' => 'rq', 'application/sparql-results+xml' => 'srx', 'application/srgs' => 'gram', 'application/srgs+xml' => 'grxml', 'application/sru+xml' => 'sru', 'application/ssml+xml' => 'ssml', 'application/tei+xml' => 'tei', 'application/thraud+xml' => 'tfi', 'application/timestamped-data' => 'tsd', 'application/vnd.3gpp.pic-bw-large' => 'plb', 'application/vnd.3gpp.pic-bw-small' => 'psb', 'application/vnd.3gpp.pic-bw-var' => 'pvb', 'application/vnd.3gpp2.tcap' => 'tcap', 'application/vnd.3m.post-it-notes' => 'pwn', 'application/vnd.accpac.simply.aso' => 'aso', 'application/vnd.accpac.simply.imp' => 'imp', 'application/vnd.acucobol' => 'acu', 'application/vnd.acucorp' => 'atc', 'application/vnd.adobe.air-application-installer-package+zip' => 'air', 'application/vnd.adobe.fxp' => 'fxp', 'application/vnd.adobe.xdp+xml' => 'xdp', 'application/vnd.adobe.xfdf' => 'xfdf', 'application/vnd.ahead.space' => 'ahead', 'application/vnd.airzip.filesecure.azf' => 'azf', 'application/vnd.airzip.filesecure.azs' => 'azs', 'application/vnd.amazon.ebook' => 'azw', 'application/vnd.americandynamics.acc' => 'acc', 'application/vnd.amiga.ami' => 'ami', 'application/vnd.android.package-archive' => 'apk', 'application/vnd.anser-web-certificate-issue-initiation' => 'cii', 'application/vnd.anser-web-funds-transfer-initiation' => 'fti', 'application/vnd.antix.game-component' => 'atx', 'application/vnd.apple.installer+xml' => 'mpkg', 'application/vnd.apple.mpegurl' => 'm3u8', 'application/vnd.aristanetworks.swi' => 'swi', 'application/vnd.astraea-software.iota' => 'iota', 'application/vnd.audiograph' => 'aep', 'application/vnd.blueice.multipass' => 'mpm', 'application/vnd.bmi' => 'bmi', 'application/vnd.businessobjects' => 'rep', 'application/vnd.chemdraw+xml' => 'cdxml', 'application/vnd.chipnuts.karaoke-mmd' => 'mmd', 'application/vnd.cinderella' => 'cdy', 'application/vnd.claymore' => 'cla', 'application/vnd.cloanto.rp9' => 'rp9', 'application/vnd.clonk.c4group' => 'c4g', 'application/vnd.cluetrust.cartomobile-config' => 'c11amc', 'application/vnd.cluetrust.cartomobile-config-pkg' => 'c11amz', 'application/vnd.commonspace' => 'csp', 'application/vnd.contact.cmsg' => 'cdbcmsg', 'application/vnd.cosmocaller' => 'cmc', 'application/vnd.crick.clicker' => 'clkx', 'application/vnd.crick.clicker.keyboard' => 'clkk', 'application/vnd.crick.clicker.palette' => 'clkp', 'application/vnd.crick.clicker.template' => 'clkt', 'application/vnd.crick.clicker.wordbank' => 'clkw', 'application/vnd.criticaltools.wbs+xml' => 'wbs', 'application/vnd.ctc-posml' => 'pml', 'application/vnd.cups-ppd' => 'ppd', 'application/vnd.curl.car' => 'car', 'application/vnd.curl.pcurl' => 'pcurl', 'application/vnd.data-vision.rdz' => 'rdz', 'application/vnd.dece.data' => 'uvf', 'application/vnd.dece.ttml+xml' => 'uvt', 'application/vnd.dece.unspecified' => 'uvx', 'application/vnd.dece.zip' => 'uvz', 'application/vnd.denovo.fcselayout-link' => 'fe_launch', 'application/vnd.dna' => 'dna', 'application/vnd.dolby.mlp' => 'mlp', 'application/vnd.dpgraph' => 'dpg', 'application/vnd.dreamfactory' => 'dfac', 'application/vnd.dvb.ait' => 'ait', 'application/vnd.dvb.service' => 'svc', 'application/vnd.dynageo' => 'geo', 'application/vnd.ecowin.chart' => 'mag', 'application/vnd.enliven' => 'nml', 'application/vnd.epson.esf' => 'esf', 'application/vnd.epson.msf' => 'msf', 'application/vnd.epson.quickanime' => 'qam', 'application/vnd.epson.salt' => 'slt', 'application/vnd.epson.ssf' => 'ssf', 'application/vnd.eszigno3+xml' => 'es3', 'application/vnd.ezpix-album' => 'ez2', 'application/vnd.ezpix-package' => 'ez3', 'application/vnd.fdf' => 'fdf', 'application/vnd.fdsn.mseed' => 'mseed', 'application/vnd.fdsn.seed' => 'seed', 'application/vnd.flographit' => 'gph', 'application/vnd.fluxtime.clip' => 'ftc', 'application/vnd.framemaker' => 'fm', 'application/vnd.frogans.fnc' => 'fnc', 'application/vnd.frogans.ltf' => 'ltf', 'application/vnd.fsc.weblaunch' => 'fsc', 'application/vnd.fujitsu.oasys' => 'oas', 'application/vnd.fujitsu.oasys2' => 'oa2', 'application/vnd.fujitsu.oasys3' => 'oa3', 'application/vnd.fujitsu.oasysgp' => 'fg5', 'application/vnd.fujitsu.oasysprs' => 'bh2', 'application/vnd.fujixerox.ddd' => 'ddd', 'application/vnd.fujixerox.docuworks' => 'xdw', 'application/vnd.fujixerox.docuworks.binder' => 'xbd', 'application/vnd.fuzzysheet' => 'fzs', 'application/vnd.genomatix.tuxedo' => 'txd', 'application/vnd.geogebra.file' => 'ggb', 'application/vnd.geogebra.tool' => 'ggt', 'application/vnd.geometry-explorer' => 'gex', 'application/vnd.geonext' => 'gxt', 'application/vnd.geoplan' => 'g2w', 'application/vnd.geospace' => 'g3w', 'application/vnd.gmx' => 'gmx', 'application/vnd.google-earth.kml+xml' => 'kml', 'application/vnd.google-earth.kmz' => 'kmz', 'application/vnd.grafeq' => 'gqf', 'application/vnd.groove-account' => 'gac', 'application/vnd.groove-help' => 'ghf', 'application/vnd.groove-identity-message' => 'gim', 'application/vnd.groove-injector' => 'grv', 'application/vnd.groove-tool-message' => 'gtm', 'application/vnd.groove-tool-template' => 'tpl', 'application/vnd.groove-vcard' => 'vcg', 'application/vnd.hal+xml' => 'hal', 'application/vnd.handheld-entertainment+xml' => 'zmm', 'application/vnd.hbci' => 'hbci', 'application/vnd.hhe.lesson-player' => 'les', 'application/vnd.hp-hpgl' => 'hpgl', 'application/vnd.hp-hpid' => 'hpid', 'application/vnd.hp-hps' => 'hps', 'application/vnd.hp-jlyt' => 'jlt', 'application/vnd.hp-pcl' => 'pcl', 'application/vnd.hp-pclxl' => 'pclxl', 'application/vnd.hydrostatix.sof-data' => 'sfd-hdstx', 'application/vnd.hzn-3d-crossword' => 'x3d', 'application/vnd.ibm.minipay' => 'mpy', 'application/vnd.ibm.modcap' => 'afp', 'application/vnd.ibm.rights-management' => 'irm', 'application/vnd.ibm.secure-container' => 'sc', 'application/vnd.iccprofile' => 'icc', 'application/vnd.igloader' => 'igl', 'application/vnd.immervision-ivp' => 'ivp', 'application/vnd.immervision-ivu' => 'ivu', 'application/vnd.insors.igm' => 'igm', 'application/vnd.intercon.formnet' => 'xpw', 'application/vnd.intergeo' => 'i2g', 'application/vnd.intu.qbo' => 'qbo', 'application/vnd.intu.qfx' => 'qfx', 'application/vnd.ipunplugged.rcprofile' => 'rcprofile', 'application/vnd.irepository.package+xml' => 'irp', 'application/vnd.is-xpr' => 'xpr', 'application/vnd.isac.fcs' => 'fcs', 'application/vnd.jam' => 'jam', 'application/vnd.jcp.javame.midlet-rms' => 'rms', 'application/vnd.jisp' => 'jisp', 'application/vnd.joost.joda-archive' => 'joda', 'application/vnd.kahootz' => 'ktz', 'application/vnd.kde.karbon' => 'karbon', 'application/vnd.kde.kchart' => 'chrt', 'application/vnd.kde.kformula' => 'kfo', 'application/vnd.kde.kivio' => 'flw', 'application/vnd.kde.kontour' => 'kon', 'application/vnd.kde.kpresenter' => 'kpr', 'application/vnd.kde.kspread' => 'ksp', 'application/vnd.kde.kword' => 'kwd', 'application/vnd.kenameaapp' => 'htke', 'application/vnd.kidspiration' => 'kia', 'application/vnd.kinar' => 'kne', 'application/vnd.koan' => 'skp', 'application/vnd.kodak-descriptor' => 'sse', 'application/vnd.las.las+xml' => 'lasxml', 'application/vnd.llamagraphics.life-balance.desktop' => 'lbd', 'application/vnd.llamagraphics.life-balance.exchange+xml' => 'lbe', 'application/vnd.lotus-1-2-3' => '123', 'application/vnd.lotus-approach' => 'apr', 'application/vnd.lotus-freelance' => 'pre', 'application/vnd.lotus-notes' => 'nsf', 'application/vnd.lotus-organizer' => 'org', 'application/vnd.lotus-screencam' => 'scm', 'application/vnd.lotus-wordpro' => 'lwp', 'application/vnd.macports.portpkg' => 'portpkg', 'application/vnd.mcd' => 'mcd', 'application/vnd.medcalcdata' => 'mc1', 'application/vnd.mediastation.cdkey' => 'cdkey', 'application/vnd.mfer' => 'mwf', 'application/vnd.mfmp' => 'mfm', 'application/vnd.micrografx.flo' => 'flo', 'application/vnd.micrografx.igx' => 'igx', 'application/vnd.mif' => 'mif', 'application/vnd.mobius.daf' => 'daf', 'application/vnd.mobius.dis' => 'dis', 'application/vnd.mobius.mbk' => 'mbk', 'application/vnd.mobius.mqy' => 'mqy', 'application/vnd.mobius.msl' => 'msl', 'application/vnd.mobius.plc' => 'plc', 'application/vnd.mobius.txf' => 'txf', 'application/vnd.mophun.application' => 'mpn', 'application/vnd.mophun.certificate' => 'mpc', 'application/vnd.mozilla.xul+xml' => 'xul', 'application/vnd.ms-artgalry' => 'cil', 'application/vnd.ms-cab-compressed' => 'cab', 'application/vnd.ms-excel' => 'xls', 'application/vnd.ms-excel.addin.macroenabled.12' => 'xlam', 'application/vnd.ms-excel.sheet.binary.macroenabled.12' => 'xlsb', 'application/vnd.ms-excel.sheet.macroenabled.12' => 'xlsm', 'application/vnd.ms-excel.template.macroenabled.12' => 'xltm', 'application/vnd.ms-fontobject' => 'eot', 'application/vnd.ms-htmlhelp' => 'chm', 'application/vnd.ms-ims' => 'ims', 'application/vnd.ms-lrm' => 'lrm', 'application/vnd.ms-officetheme' => 'thmx', 'application/vnd.ms-pki.seccat' => 'cat', 'application/vnd.ms-pki.stl' => 'stl', 'application/vnd.ms-powerpoint' => 'ppt', 'application/vnd.ms-powerpoint.addin.macroenabled.12' => 'ppam', 'application/vnd.ms-powerpoint.presentation.macroenabled.12' => 'pptm', 'application/vnd.ms-powerpoint.slide.macroenabled.12' => 'sldm', 'application/vnd.ms-powerpoint.slideshow.macroenabled.12' => 'ppsm', 'application/vnd.ms-powerpoint.template.macroenabled.12' => 'potm', 'application/vnd.ms-project' => 'mpp', 'application/vnd.ms-word.document.macroenabled.12' => 'docm', 'application/vnd.ms-word.template.macroenabled.12' => 'dotm', 'application/vnd.ms-works' => 'wps', 'application/vnd.ms-wpl' => 'wpl', 'application/vnd.ms-xpsdocument' => 'xps', 'application/vnd.mseq' => 'mseq', 'application/vnd.musician' => 'mus', 'application/vnd.muvee.style' => 'msty', 'application/vnd.mynfc' => 'taglet', 'application/vnd.neurolanguage.nlu' => 'nlu', 'application/vnd.noblenet-directory' => 'nnd', 'application/vnd.noblenet-sealer' => 'nns', 'application/vnd.noblenet-web' => 'nnw', 'application/vnd.nokia.n-gage.data' => 'ngdat', 'application/vnd.nokia.n-gage.symbian.install' => 'n-gage', 'application/vnd.nokia.radio-preset' => 'rpst', 'application/vnd.nokia.radio-presets' => 'rpss', 'application/vnd.novadigm.edm' => 'edm', 'application/vnd.novadigm.edx' => 'edx', 'application/vnd.novadigm.ext' => 'ext', 'application/vnd.oasis.opendocument.chart' => 'odc', 'application/vnd.oasis.opendocument.chart-template' => 'otc', 'application/vnd.oasis.opendocument.database' => 'odb', 'application/vnd.oasis.opendocument.formula' => 'odf', 'application/vnd.oasis.opendocument.formula-template' => 'odft', 'application/vnd.oasis.opendocument.graphics' => 'odg', 'application/vnd.oasis.opendocument.graphics-template' => 'otg', 'application/vnd.oasis.opendocument.image' => 'odi', 'application/vnd.oasis.opendocument.image-template' => 'oti', 'application/vnd.oasis.opendocument.presentation' => 'odp', 'application/vnd.oasis.opendocument.presentation-template' => 'otp', 'application/vnd.oasis.opendocument.spreadsheet' => 'ods', 'application/vnd.oasis.opendocument.spreadsheet-template' => 'ots', 'application/vnd.oasis.opendocument.text' => 'odt', 'application/vnd.oasis.opendocument.text-master' => 'odm', 'application/vnd.oasis.opendocument.text-template' => 'ott', 'application/vnd.oasis.opendocument.text-web' => 'oth', 'application/vnd.olpc-sugar' => 'xo', 'application/vnd.oma.dd2+xml' => 'dd2', 'application/vnd.openofficeorg.extension' => 'oxt', 'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx', 'application/vnd.openxmlformats-officedocument.presentationml.slide' => 'sldx', 'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => 'ppsx', 'application/vnd.openxmlformats-officedocument.presentationml.template' => 'potx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => 'xltx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => 'dotx', 'application/vnd.osgeo.mapguide.package' => 'mgp', 'application/vnd.osgi.dp' => 'dp', 'application/vnd.palm' => 'pdb', 'application/vnd.pawaafile' => 'paw', 'application/vnd.pg.format' => 'str', 'application/vnd.pg.osasli' => 'ei6', 'application/vnd.picsel' => 'efif', 'application/vnd.pmi.widget' => 'wg', 'application/vnd.pocketlearn' => 'plf', 'application/vnd.powerbuilder6' => 'pbd', 'application/vnd.previewsystems.box' => 'box', 'application/vnd.proteus.magazine' => 'mgz', 'application/vnd.publishare-delta-tree' => 'qps', 'application/vnd.pvi.ptid1' => 'ptid', 'application/vnd.quark.quarkxpress' => 'qxd', 'application/vnd.realvnc.bed' => 'bed', 'application/vnd.recordare.musicxml' => 'mxl', 'application/vnd.recordare.musicxml+xml' => 'musicxml', 'application/vnd.rig.cryptonote' => 'cryptonote', 'application/vnd.rim.cod' => 'cod', 'application/vnd.rn-realmedia' => 'rm', 'application/vnd.route66.link66+xml' => 'link66', 'application/vnd.sailingtracker.track' => 'st', 'application/vnd.seemail' => 'see', 'application/vnd.sema' => 'sema', 'application/vnd.semd' => 'semd', 'application/vnd.semf' => 'semf', 'application/vnd.shana.informed.formdata' => 'ifm', 'application/vnd.shana.informed.formtemplate' => 'itp', 'application/vnd.shana.informed.interchange' => 'iif', 'application/vnd.shana.informed.package' => 'ipk', 'application/vnd.simtech-mindmapper' => 'twd', 'application/vnd.smaf' => 'mmf', 'application/vnd.smart.teacher' => 'teacher', 'application/vnd.solent.sdkm+xml' => 'sdkm', 'application/vnd.spotfire.dxp' => 'dxp', 'application/vnd.spotfire.sfs' => 'sfs', 'application/vnd.stardivision.calc' => 'sdc', 'application/vnd.stardivision.draw' => 'sda', 'application/vnd.stardivision.impress' => 'sdd', 'application/vnd.stardivision.math' => 'smf', 'application/vnd.stardivision.writer' => 'sdw', 'application/vnd.stardivision.writer-global' => 'sgl', 'application/vnd.stepmania.package' => 'smzip', 'application/vnd.stepmania.stepchart' => 'sm', 'application/vnd.sun.xml.calc' => 'sxc', 'application/vnd.sun.xml.calc.template' => 'stc', 'application/vnd.sun.xml.draw' => 'sxd', 'application/vnd.sun.xml.draw.template' => 'std', 'application/vnd.sun.xml.impress' => 'sxi', 'application/vnd.sun.xml.impress.template' => 'sti', 'application/vnd.sun.xml.math' => 'sxm', 'application/vnd.sun.xml.writer' => 'sxw', 'application/vnd.sun.xml.writer.global' => 'sxg', 'application/vnd.sun.xml.writer.template' => 'stw', 'application/vnd.sus-calendar' => 'sus', 'application/vnd.svd' => 'svd', 'application/vnd.symbian.install' => 'sis', 'application/vnd.syncml+xml' => 'xsm', 'application/vnd.syncml.dm+wbxml' => 'bdm', 'application/vnd.syncml.dm+xml' => 'xdm', 'application/vnd.tao.intent-module-archive' => 'tao', 'application/vnd.tcpdump.pcap' => 'pcap', 'application/vnd.tmobile-livetv' => 'tmo', 'application/vnd.trid.tpt' => 'tpt', 'application/vnd.triscape.mxs' => 'mxs', 'application/vnd.trueapp' => 'tra', 'application/vnd.ufdl' => 'ufd', 'application/vnd.uiq.theme' => 'utz', 'application/vnd.umajin' => 'umj', 'application/vnd.unity' => 'unityweb', 'application/vnd.uoml+xml' => 'uoml', 'application/vnd.vcx' => 'vcx', 'application/vnd.visio' => 'vsd', 'application/vnd.visionary' => 'vis', 'application/vnd.vsf' => 'vsf', 'application/vnd.wap.wbxml' => 'wbxml', 'application/vnd.wap.wmlc' => 'wmlc', 'application/vnd.wap.wmlscriptc' => 'wmlsc', 'application/vnd.webturbo' => 'wtb', 'application/vnd.wolfram.player' => 'nbp', 'application/vnd.wordperfect' => 'wpd', 'application/vnd.wqd' => 'wqd', 'application/vnd.wt.stf' => 'stf', 'application/vnd.xara' => 'xar', 'application/vnd.xfdl' => 'xfdl', 'application/vnd.yamaha.hv-dic' => 'hvd', 'application/vnd.yamaha.hv-script' => 'hvs', 'application/vnd.yamaha.hv-voice' => 'hvp', 'application/vnd.yamaha.openscoreformat' => 'osf', 'application/vnd.yamaha.openscoreformat.osfpvg+xml' => 'osfpvg', 'application/vnd.yamaha.smaf-audio' => 'saf', 'application/vnd.yamaha.smaf-phrase' => 'spf', 'application/vnd.yellowriver-custom-menu' => 'cmp', 'application/vnd.zul' => 'zir', 'application/vnd.zzazz.deck+xml' => 'zaz', 'application/voicexml+xml' => 'vxml', 'application/widget' => 'wgt', 'application/winhlp' => 'hlp', 'application/wsdl+xml' => 'wsdl', 'application/wspolicy+xml' => 'wspolicy', 'application/x-7z-compressed' => '7z', 'application/x-abiword' => 'abw', 'application/x-ace-compressed' => 'ace', 'application/x-authorware-bin' => 'aab', 'application/x-authorware-map' => 'aam', 'application/x-authorware-seg' => 'aas', 'application/x-bcpio' => 'bcpio', 'application/x-bittorrent' => 'torrent', 'application/x-bzip' => 'bz', 'application/x-bzip2' => 'bz2', 'application/x-cdlink' => 'vcd', 'application/x-chat' => 'chat', 'application/x-chess-pgn' => 'pgn', 'application/x-cpio' => 'cpio', 'application/x-csh' => 'csh', 'application/x-debian-package' => 'deb', 'application/x-director' => 'dir', 'application/x-doom' => 'wad', 'application/x-dtbncx+xml' => 'ncx', 'application/x-dtbook+xml' => 'dtb', 'application/x-dtbresource+xml' => 'res', 'application/x-dvi' => 'dvi', 'application/x-font-bdf' => 'bdf', 'application/x-font-ghostscript' => 'gsf', 'application/x-font-linux-psf' => 'psf', 'application/x-font-otf' => 'otf', 'application/x-font-pcf' => 'pcf', 'application/x-font-snf' => 'snf', 'application/x-font-ttf' => 'ttf', 'application/x-font-type1' => 'pfa', 'application/x-font-woff' => 'woff', 'application/x-futuresplash' => 'spl', 'application/x-gnumeric' => 'gnumeric', 'application/x-gtar' => 'gtar', 'application/x-hdf' => 'hdf', 'application/x-java-jnlp-file' => 'jnlp', 'application/x-latex' => 'latex', 'application/x-mobipocket-ebook' => 'prc', 'application/x-ms-application' => 'application', 'application/x-ms-wmd' => 'wmd', 'application/x-ms-wmz' => 'wmz', 'application/x-ms-xbap' => 'xbap', 'application/x-msaccess' => 'mdb', 'application/x-msbinder' => 'obd', 'application/x-mscardfile' => 'crd', 'application/x-msclip' => 'clp', 'application/x-msdownload' => 'exe', 'application/x-msmediaview' => 'mvb', 'application/x-msmetafile' => 'wmf', 'application/x-msmoney' => 'mny', 'application/x-mspublisher' => 'pub', 'application/x-msschedule' => 'scd', 'application/x-msterminal' => 'trm', 'application/x-mswrite' => 'wri', 'application/x-netcdf' => 'nc', 'application/x-pkcs12' => 'p12', 'application/x-pkcs7-certificates' => 'p7b', 'application/x-pkcs7-certreqresp' => 'p7r', 'application/x-rar-compressed' => 'rar', 'application/x-rar' => 'rar', 'application/x-sh' => 'sh', 'application/x-shar' => 'shar', 'application/x-shockwave-flash' => 'swf', 'application/x-silverlight-app' => 'xap', 'application/x-stuffit' => 'sit', 'application/x-stuffitx' => 'sitx', 'application/x-sv4cpio' => 'sv4cpio', 'application/x-sv4crc' => 'sv4crc', 'application/x-tar' => 'tar', 'application/x-tcl' => 'tcl', 'application/x-tex' => 'tex', 'application/x-tex-tfm' => 'tfm', 'application/x-texinfo' => 'texinfo', 'application/x-ustar' => 'ustar', 'application/x-wais-source' => 'src', 'application/x-x509-ca-cert' => 'der', 'application/x-xfig' => 'fig', 'application/x-xpinstall' => 'xpi', 'application/xcap-diff+xml' => 'xdf', 'application/xenc+xml' => 'xenc', 'application/xhtml+xml' => 'xhtml', 'application/xml' => 'xml', 'application/xml-dtd' => 'dtd', 'application/xop+xml' => 'xop', 'application/xslt+xml' => 'xslt', 'application/xspf+xml' => 'xspf', 'application/xv+xml' => 'mxml', 'application/yang' => 'yang', 'application/yin+xml' => 'yin', 'application/zip' => 'zip', 'audio/adpcm' => 'adp', 'audio/basic' => 'au', 'audio/midi' => 'mid', 'audio/mp4' => 'mp4a', 'audio/mpeg' => 'mpga', 'audio/ogg' => 'oga', 'audio/vnd.dece.audio' => 'uva', 'audio/vnd.digital-winds' => 'eol', 'audio/vnd.dra' => 'dra', 'audio/vnd.dts' => 'dts', 'audio/vnd.dts.hd' => 'dtshd', 'audio/vnd.lucent.voice' => 'lvp', 'audio/vnd.ms-playready.media.pya' => 'pya', 'audio/vnd.nuera.ecelp4800' => 'ecelp4800', 'audio/vnd.nuera.ecelp7470' => 'ecelp7470', 'audio/vnd.nuera.ecelp9600' => 'ecelp9600', 'audio/vnd.rip' => 'rip', 'audio/webm' => 'weba', 'audio/x-aac' => 'aac', 'audio/x-aiff' => 'aif', 'audio/x-mpegurl' => 'm3u', 'audio/x-ms-wax' => 'wax', 'audio/x-ms-wma' => 'wma', 'audio/x-pn-realaudio' => 'ram', 'audio/x-pn-realaudio-plugin' => 'rmp', 'audio/x-wav' => 'wav', 'chemical/x-cdx' => 'cdx', 'chemical/x-cif' => 'cif', 'chemical/x-cmdf' => 'cmdf', 'chemical/x-cml' => 'cml', 'chemical/x-csml' => 'csml', 'chemical/x-xyz' => 'xyz', 'image/bmp' => 'bmp', 'image/cgm' => 'cgm', 'image/g3fax' => 'g3', 'image/gif' => 'gif', 'image/ief' => 'ief', 'image/jpeg' => 'jpeg', 'image/ktx' => 'ktx', 'image/png' => 'png', 'image/prs.btif' => 'btif', 'image/svg+xml' => 'svg', 'image/tiff' => 'tiff', 'image/vnd.adobe.photoshop' => 'psd', 'image/vnd.dece.graphic' => 'uvi', 'image/vnd.dvb.subtitle' => 'sub', 'image/vnd.djvu' => 'djvu', 'image/vnd.dwg' => 'dwg', 'image/vnd.dxf' => 'dxf', 'image/vnd.fastbidsheet' => 'fbs', 'image/vnd.fpx' => 'fpx', 'image/vnd.fst' => 'fst', 'image/vnd.fujixerox.edmics-mmr' => 'mmr', 'image/vnd.fujixerox.edmics-rlc' => 'rlc', 'image/vnd.ms-modi' => 'mdi', 'image/vnd.net-fpx' => 'npx', 'image/vnd.wap.wbmp' => 'wbmp', 'image/vnd.xiff' => 'xif', 'image/webp' => 'webp', 'image/x-cmu-raster' => 'ras', 'image/x-cmx' => 'cmx', 'image/x-freehand' => 'fh', 'image/x-icon' => 'ico', 'image/x-pcx' => 'pcx', 'image/x-pict' => 'pic', 'image/x-portable-anymap' => 'pnm', 'image/x-portable-bitmap' => 'pbm', 'image/x-portable-graymap' => 'pgm', 'image/x-portable-pixmap' => 'ppm', 'image/x-rgb' => 'rgb', 'image/x-xbitmap' => 'xbm', 'image/x-xpixmap' => 'xpm', 'image/x-xwindowdump' => 'xwd', 'message/rfc822' => 'eml', 'model/iges' => 'igs', 'model/mesh' => 'msh', 'model/vnd.collada+xml' => 'dae', 'model/vnd.dwf' => 'dwf', 'model/vnd.gdl' => 'gdl', 'model/vnd.gtw' => 'gtw', 'model/vnd.mts' => 'mts', 'model/vnd.vtu' => 'vtu', 'model/vrml' => 'wrl', 'text/calendar' => 'ics', 'text/css' => 'css', 'text/csv' => 'csv', 'text/html' => 'html', 'text/n3' => 'n3', 'text/plain' => 'txt', 'text/prs.lines.tag' => 'dsc', 'text/richtext' => 'rtx', 'text/sgml' => 'sgml', 'text/tab-separated-values' => 'tsv', 'text/troff' => 't', 'text/turtle' => 'ttl', 'text/uri-list' => 'uri', 'text/vcard' => 'vcard', 'text/vnd.curl' => 'curl', 'text/vnd.curl.dcurl' => 'dcurl', 'text/vnd.curl.scurl' => 'scurl', 'text/vnd.curl.mcurl' => 'mcurl', 'text/vnd.dvb.subtitle' => 'sub', 'text/vnd.fly' => 'fly', 'text/vnd.fmi.flexstor' => 'flx', 'text/vnd.graphviz' => 'gv', 'text/vnd.in3d.3dml' => '3dml', 'text/vnd.in3d.spot' => 'spot', 'text/vnd.sun.j2me.app-descriptor' => 'jad', 'text/vnd.wap.wml' => 'wml', 'text/vnd.wap.wmlscript' => 'wmls', 'text/x-asm' => 's', 'text/x-c' => 'c', 'text/x-fortran' => 'f', 'text/x-pascal' => 'p', 'text/x-java-source' => 'java', 'text/x-setext' => 'etx', 'text/x-uuencode' => 'uu', 'text/x-vcalendar' => 'vcs', 'text/x-vcard' => 'vcf', 'video/3gpp' => '3gp', 'video/3gpp2' => '3g2', 'video/h261' => 'h261', 'video/h263' => 'h263', 'video/h264' => 'h264', 'video/jpeg' => 'jpgv', 'video/jpm' => 'jpm', 'video/mj2' => 'mj2', 'video/mp4' => 'mp4', 'video/mpeg' => 'mpeg', 'video/ogg' => 'ogv', 'video/quicktime' => 'qt', 'video/vnd.dece.hd' => 'uvh', 'video/vnd.dece.mobile' => 'uvm', 'video/vnd.dece.pd' => 'uvp', 'video/vnd.dece.sd' => 'uvs', 'video/vnd.dece.video' => 'uvv', 'video/vnd.dvb.file' => 'dvb', 'video/vnd.fvt' => 'fvt', 'video/vnd.mpegurl' => 'mxu', 'video/vnd.ms-playready.media.pyv' => 'pyv', 'video/vnd.uvvu.mp4' => 'uvu', 'video/vnd.vivo' => 'viv', 'video/webm' => 'webm', 'video/x-f4v' => 'f4v', 'video/x-fli' => 'fli', 'video/x-flv' => 'flv', 'video/x-m4v' => 'm4v', 'video/x-ms-asf' => 'asf', 'video/x-ms-wm' => 'wm', 'video/x-ms-wmv' => 'wmv', 'video/x-ms-wmx' => 'wmx', 'video/x-ms-wvx' => 'wvx', 'video/x-msvideo' => 'avi', 'video/x-sgi-movie' => 'movie', 'x-conference/x-cooltalk' => 'ice',);
		public function guess($mimeType)
		{
			return isset($this->defaultExtensions[$mimeType]) ? $this->defaultExtensions[$mimeType] : null;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser.php
	 */
	class MimeTypeGuesser implements MimeTypeGuesserInterface
	{
		private static $instance = null;
		protected $guessers = array();
		public static function getInstance()
		{
			if (null === self::$instance) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		private function __construct()
		{
			if (FileBinaryMimeTypeGuesser::isSupported()) {
				$this->register(new FileBinaryMimeTypeGuesser());
			}
			if (FileinfoMimeTypeGuesser::isSupported()) {
				$this->register(new FileinfoMimeTypeGuesser());
			}
		}
		public function register(MimeTypeGuesserInterface $guesser)
		{
			array_unshift($this->guessers, $guesser);
		}
		public function guess($path)
		{
			if (!is_file($path)) {
				throw new FileNotFoundException($path);
			}
			if (!is_readable($path)) {
				throw new AccessDeniedException($path);
			}
			if (!$this->guessers) {
				throw new \LogicException('Unable to guess the mime type as no guessers are available (Did you enable the php_fileinfo extension?)');
			}
			foreach ($this->guessers as $guesser) {
				if (null !== $mimeType = $guesser->guess($path)) {
					return $mimeType;
				}
			}
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface.php
	 */
	interface MimeTypeGuesserInterface
	{
		public function guess($path);
	}
}
namespace Laravel\Session\Drivers
{
	use Laravel\Crypter;
	use Laravel\Str;
	use Laravel\Database\Connection;
	use Laravel\Config;
	use Laravel\Cookie as C;
	/**
	 * laravel\session\drivers\apc.php
	 */
	class APC extends Driver
	{
		private $apc;
		public function __construct(\Laravel\Cache\Drivers\APC $apc)
		{
			$this->apc = $apc;
		}
		public function load($id)
		{
			return $this->apc->get($id);
		}
		public function save($session, $config, $exists)
		{
			$this->apc->put($session['id'], $session, $config['lifetime']);
		}
		public function delete($id)
		{
			$this->apc->forget($id);
		}
	}
	/**
	 * laravel\session\drivers\cookie.php
	 */
	class Cookie extends Driver
	{
		const payload = 'session_payload';
		public function load($id)
		{
			if (C::has(Cookie::payload)) {
				return unserialize(Crypter::decrypt(C::get(Cookie::payload)));
			}
		}
		public function save($session, $config, $exists)
		{
			extract($config, EXTR_SKIP);
			$payload = Crypter::encrypt(serialize($session));
			C::put(Cookie::payload, $payload, $lifetime, $path, $domain);
		}
		public function delete($id)
		{
			C::forget(Cookie::payload);
		}
	}
	/**
	 * laravel\session\drivers\database.php
	 */
	class Database extends Driver implements Sweeper
	{
		protected $connection;
		public function __construct(Connection $connection)
		{
			$this->connection = $connection;
		}
		public function load($id)
		{
			$session = $this->table()->find($id);
			if (!is_null($session)) {
				return array('id' => $session->id, 'last_activity' => $session->last_activity, 'data' => unserialize($session->data));
			}
		}
		public function save($session, $config, $exists)
		{
			if ($exists) {
				$this->table()->where('id', '=', $session['id'])->update(array('last_activity' => $session['last_activity'], 'data' => serialize($session['data']),));
			} else {
				$this->table()->insert(array('id' => $session['id'], 'last_activity' => $session['last_activity'], 'data' => serialize($session['data'])));
			}
		}
		public function delete($id)
		{
			$this->table()->delete($id);
		}
		public function sweep($expiration)
		{
			$this->table()->where('last_activity', '<', $expiration)->delete();
		}
		private function table()
		{
			return $this->connection->table(Config::get('session.table'));
		}
	}
	/**
	 * laravel\session\drivers\driver.php
	 */
	abstract class Driver
	{
		abstract public function load($id);
		abstract public function save($session, $config, $exists);
		abstract public function delete($id);
		public function fresh()
		{
			return array('id' => $this->id(), 'data' => array(':new:' => array(), ':old:' => array(),));
		}
		public function id()
		{
			$session = array();
			if ($this instanceof Cookie) {
				return Str::random(40);
			}
			do {
				$session = $this->load($id = Str::random(40));
			} while (!is_null($session));
			return $id;
		}
	}
	/**
	 * laravel\session\drivers\file.php
	 */
	class File extends Driver implements Sweeper
	{
		private $path;
		public function __construct($path)
		{
			$this->path = $path;
		}
		public function load($id)
		{
			if (file_exists($path = $this->path . $id)) {
				return unserialize(file_get_contents($path));
			}
		}
		public function save($session, $config, $exists)
		{
			file_put_contents($this->path . $session['id'], serialize($session), LOCK_EX);
		}
		public function delete($id)
		{
			if (file_exists($this->path . $id)) {
				@unlink($this->path . $id);
			}
		}
		public function sweep($expiration)
		{
			$files = glob($this->path . '*');
			if ($files === false) return;
			foreach ($files as $file) {
				if (filetype($file) == 'file' and filemtime($file) < $expiration) {
					@unlink($file);
				}
			}
		}
	}
	/**
	 * laravel\session\drivers\memcached.php
	 */
	class Memcached extends Driver
	{
		private $memcached;
		public function __construct(\Laravel\Cache\Drivers\Memcached $memcached)
		{
			$this->memcached = $memcached;
		}
		public function load($id)
		{
			return $this->memcached->get($id);
		}
		public function save($session, $config, $exists)
		{
			$this->memcached->put($session['id'], $session, $config['lifetime']);
		}
		public function delete($id)
		{
			$this->memcached->forget($id);
		}
	}
	/**
	 * laravel\session\drivers\memory.php
	 */
	class Memory extends Driver
	{
		public $session;
		public function load($id)
		{
			return $this->session;
		}
		public function save($session, $config, $exists)
		{
		}
		public function delete($id)
		{
		}
	}
	/**
	 * laravel\session\drivers\redis.php
	 */
	class Redis extends Driver
	{
		protected $redis;
		public function __construct(\Laravel\Cache\Drivers\Redis $redis)
		{
			$this->redis = $redis;
		}
		public function load($id)
		{
			return $this->redis->get($id);
		}
		public function save($session, $config, $exists)
		{
			$this->redis->put($session['id'], $session, $config['lifetime']);
		}
		public function delete($id)
		{
			$this->redis->forget($id);
		}
	}
	/**
	 * laravel\session\drivers\sweeper.php
	 */
	interface Sweeper
	{
		public function sweep($expiration);
	}
}
namespace Laravel\Routing {
	use Closure;
	use Laravel\IoC;
	use Laravel\Bundle;
	use Laravel\Response;
	use Laravel\Redirect;
	use Laravel\URI;
	use FilesystemIterator as fIterator;
	use Laravel\Request;
	use Laravel\View;
	use Laravel\Str;
	use Laravel\Event;
	/**
	 * laravel\routing\controller.php
	 */
	abstract class Controller
	{
		public $layout;
		public $bundle;
		public $restful = false;
		protected $filters = array();
		const factory = 'laravel.controller.factory';
		public function __construct()
		{
			if (!is_null($this->layout)) {
				$this->layout = $this->layout();
			}
		}
		public static function detect($bundle = DEFAULT_BUNDLE, $directory = null)
		{
			if (is_null($directory)) {
				$directory = Bundle::path($bundle) . 'controllers';
			}
			$root = Bundle::path($bundle) . 'controllers' . DS;
			$controllers = array();
			$items = new fIterator($directory, fIterator::SKIP_DOTS);
			foreach ($items as $item) {
				if ($item->isDir()) {
					$nested = static ::detect($bundle, $item->getRealPath());
					$controllers = array_merge($controllers, $nested);
				} else {
					$controller = str_replace(array($root, EXT), '', $item->getRealPath());
					$controller = str_replace(DS, '.', $controller);
					$controllers[] = Bundle::identifier($bundle, $controller);
				}
			}
			return $controllers;
		}
		public static function call($destination, $parameters = array())
		{
			static ::references($destination, $parameters);
			list($bundle, $destination) = Bundle::parse($destination);
			Bundle::start($bundle);
			list($name, $method) = explode('@', $destination);
			$controller = static ::resolve($bundle, $name);
			if (!is_null($route = Request::route())) {
				$route->controller = $name;
				$route->controller_action = $method;
			}
			if (is_null($controller)) {
				return Event::first('404');
			}
			return $controller->execute($method, $parameters);
		}
		protected static function references(&$destination, &$parameters)
		{
			foreach ($parameters as $key => $value) {
				if (!is_string($value)) continue;
				$search = '(:' . ($key + 1) . ')';
				$destination = str_replace($search, $value, $destination, $count);
				if ($count > 0) unset($parameters[$key]);
			}
			return array($destination, $parameters);
		}
		public static function resolve($bundle, $controller)
		{
			if (!static ::load($bundle, $controller)) return;
			$identifier = Bundle::identifier($bundle, $controller);
			$resolver = 'controller: ' . $identifier;
			if (IoC::registered($resolver)) {
				return IoC::resolve($resolver);
			}
			$controller = static ::format($bundle, $controller);
			if (Event::listeners(static ::factory)) {
				return Event::first(static ::factory, $controller);
			} else {
				return new $controller;
			}
		}
		protected static function load($bundle, $controller)
		{
			$controller = strtolower(str_replace('.', '/', $controller));
			if (file_exists($path = Bundle::path($bundle) . 'controllers/' . $controller . EXT)) {
				require_once $path;
				return true;
			}
			return false;
		}
		protected static function format($bundle, $controller)
		{
			return Bundle::class_prefix($bundle) . Str::classify($controller) . '_Controller';
		}
		public function execute($method, $parameters = array())
		{
			$filters = $this->filters('before', $method);
			$response = Filter::run($filters, array(), true);
			if (is_null($response)) {
				$this->before();
				$response = $this->response($method, $parameters);
			}
			$response = Response::prepare($response);
			$this->after($response);
			Filter::run($this->filters('after', $method), array($response));
			return $response;
		}
		public function response($method, $parameters = array())
		{
			if ($this->restful) {
				$action = strtolower(Request::method()) . '_' . $method;
			} else {
				$action = "action_{$method}";
			}
			$response = call_user_func_array(array($this, $action), $parameters);
			if (is_null($response) and !is_null($this->layout)) {
				$response = $this->layout;
			}
			return $response;
		}
		protected function filter($event, $filters, $parameters = null)
		{
			$this->filters[$event][] = new Filter_Collection($filters, $parameters);
			return $this->filters[$event][count($this->filters[$event]) - 1];
		}
		protected function filters($event, $method)
		{
			if (!isset($this->filters[$event])) return array();
			$filters = array();
			foreach ($this->filters[$event] as $collection) {
				if ($collection->applies($method)) {
					$filters[] = $collection;
				}
			}
			return $filters;
		}
		public function layout()
		{
			if (starts_with($this->layout, 'name: ')) {
				return View::of(substr($this->layout, 6));
			}
			return View::make($this->layout);
		}
		public function before()
		{
		}
		public function after($response)
		{
		}
		public function __call($method, $parameters)
		{
			return Response::error('404');
		}
		public function __get($key)
		{
			if (IoC::registered($key)) {
				return IoC::resolve($key);
			}
		}
	}
	/**
	 * laravel\routing\filter.php
	 */
	class Filter
	{
		public static $filters = array();
		public static $patterns = array();
		public static $aliases = array();
		public static function register($name, $callback)
		{
			if (isset(static ::$aliases[$name])) $name = static ::$aliases[$name];
			if (starts_with($name, 'pattern: ')) {
				foreach (explode(', ', substr($name, 9)) as $pattern) {
					static ::$patterns[$pattern] = $callback;
				}
			} else {
				static ::$filters[$name] = $callback;
			}
		}
		public static function alias($filter, $alias)
		{
			static ::$aliases[$alias] = $filter;
		}
		public static function parse($filters)
		{
			return (is_string($filters)) ? explode('|', $filters) : (array)$filters;
		}
		public static function run($collections, $pass = array(), $override = false)
		{
			foreach ($collections as $collection) {
				foreach ($collection->filters as $filter) {
					list($filter, $parameters) = $collection->get($filter);
					Bundle::start(Bundle::name($filter));
					if (!isset(static ::$filters[$filter])) continue;
					$callback = static ::$filters[$filter];
					$response = call_user_func_array($callback, array_merge($pass, $parameters));
					if (!is_null($response) and $override) {
						return $response;
					}
				}
			}
		}
	}
	class Filter_Collection
	{
		public $filters = array();
		public $parameters;
		public $only = array();
		public $except = array();
		public $methods = array();
		public function __construct($filters, $parameters = null)
		{
			$this->parameters = $parameters;
			$this->filters = Filter::parse($filters);
		}
		public function get($filter)
		{
			if (!is_null($this->parameters)) {
				return array($filter, $this->parameters());
			}
			if (($colon = strpos(Bundle::element($filter), ':')) !== false) {
				$parameters = explode(',', substr(Bundle::element($filter), $colon + 1));
				if (($bundle = Bundle::name($filter)) !== DEFAULT_BUNDLE) {
					$colon = strlen($bundle . '::') + $colon;
				}
				return array(substr($filter, 0, $colon), $parameters);
			}
			return array($filter, array());
		}
		protected function parameters()
		{
			if ($this->parameters instanceof Closure) {
				$this->parameters = call_user_func($this->parameters);
			}
			return $this->parameters;
		}
		public function applies($method)
		{
			if (count($this->only) > 0 and !in_array($method, $this->only)) {
				return false;
			}
			if (count($this->except) > 0 and in_array($method, $this->except)) {
				return false;
			}
			$request = strtolower(Request::method());
			if (count($this->methods) > 0 and !in_array($request, $this->methods)) {
				return false;
			}
			return true;
		}
		public function except($methods)
		{
			$this->except = (array)$methods;
			return $this;
		}
		public function only($methods)
		{
			$this->only = (array)$methods;
			return $this;
		}
		public function on($methods)
		{
			$this->methods = array_map('strtolower', (array)$methods);
			return $this;
		}
	}
	/**
	 * laravel\routing\route.php
	 */
	class Route
	{
		public $uri;
		public $method;
		public $bundle;
		public $controller;
		public $controller_action;
		public $action;
		public $parameters;
		public function __construct($method, $uri, $action, $parameters = array())
		{
			$this->uri = $uri;
			$this->method = $method;
			$this->action = $action;
			$this->bundle = Bundle::handles($uri);
			$this->parameters($action, $parameters);
		}
		protected function parameters($action, $parameters)
		{
			$defaults = (array)array_get($action, 'defaults');
			if (count($defaults) > count($parameters)) {
				$defaults = array_slice($defaults, count($parameters));
				$parameters = array_merge($parameters, $defaults);
			}
			$this->parameters = $parameters;
		}
		public function call()
		{
			$response = Filter::run($this->filters('before'), array(), true);
			if (is_null($response)) {
				$response = $this->response();
			}
			$response = Response::prepare($response);
			Filter::run($this->filters('after'), array(&$response));
			return $response;
		}
		public function response()
		{
			$delegate = $this->delegate();
			if (!is_null($delegate)) {
				return Controller::call($delegate, $this->parameters);
			}
			$handler = $this->handler();
			if (!is_null($handler)) {
				return call_user_func_array($handler, $this->parameters);
			}
		}
		protected function filters($event)
		{
			$global = Bundle::prefix($this->bundle) . $event;
			$filters = array_unique(array($event, $global));
			if (isset($this->action[$event])) {
				$assigned = Filter::parse($this->action[$event]);
				$filters = array_merge($filters, $assigned);
			}
			if ($event == 'before') {
				$filters = array_merge($filters, $this->patterns());
			}
			return array(new Filter_Collection($filters));
		}
		protected function patterns()
		{
			$filters = array();
			foreach (Filter::$patterns as $pattern => $filter) {
				if (Str::is($pattern, $this->uri)) {
					if (is_array($filter)) {
						list($filter, $callback) = array_values($filter);
						Filter::register($filter, $callback);
					}
					$filters[] = $filter;
				}
			}
			return (array)$filters;
		}
		protected function delegate()
		{
			return array_get($this->action, 'uses');
		}
		protected function handler()
		{
			return array_first($this->action, function ($key, $value)
			{
				return $value instanceof Closure;
			});
		}
		public function is($name)
		{
			return array_get($this->action, 'as') === $name;
		}
		public static function controller($controllers, $defaults = 'index')
		{
			Router::controller($controllers, $defaults);
		}
		public static function secure_controller($controllers, $defaults = 'index')
		{
			Router::controller($controllers, $defaults, true);
		}
		public static function get($route, $action)
		{
			Router::register('GET', $route, $action);
		}
		public static function post($route, $action)
		{
			Router::register('POST', $route, $action);
		}
		public static function put($route, $action)
		{
			Router::register('PUT', $route, $action);
		}
		public static function delete($route, $action)
		{
			Router::register('DELETE', $route, $action);
		}
		public static function any($route, $action)
		{
			Router::register('*', $route, $action);
		}
		public static function group($attributes, Closure $callback)
		{
			Router::group($attributes, $callback);
		}
		public static function share($routes, $action)
		{
			Router::share($routes, $action);
		}
		public static function secure($method, $route, $action)
		{
			Router::secure($method, $route, $action);
		}
		public static function filter($name, $callback)
		{
			Filter::register($name, $callback);
		}
		public static function forward($method, $uri)
		{
			return Router::route(strtoupper($method), $uri)->call();
		}
	}
	/**
	 * laravel\routing\router.php
	 */
	class Router
	{
		public static $names = array();
		public static $uses = array();
		public static $routes = array('GET' => array(), 'POST' => array(), 'PUT' => array(), 'DELETE' => array(), 'PATCH' => array(), 'HEAD' => array(),);
		public static $fallback = array('GET' => array(), 'POST' => array(), 'PUT' => array(), 'DELETE' => array(), 'PATCH' => array(), 'HEAD' => array(),);
		public static $group;
		public static $bundle;
		public static $segments = 5;
		public static $patterns = array('(:num)' => '([0-9]+)', '(:any)' => '([a-zA-Z0-9\.\-_%=]+)', '(:segment)' => '([^/]+)', '(:all)' => '(.*)',);
		public static $optional = array('/(:num?)' => '(?:/([0-9]+)', '/(:any?)' => '(?:/([a-zA-Z0-9\.\-_%=]+)', '/(:segment?)' => '(?:/([^/]+)', '/(:all?)' => '(?:/(.*)',);
		public static $methods = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD');
		public static function secure($method, $route, $action)
		{
			$action = static ::action($action);
			$action['https'] = true;
			static ::register($method, $route, $action);
		}
		public static function share($routes, $action)
		{
			foreach ($routes as $route) {
				static ::register($route[0], $route[1], $action);
			}
		}
		public static function group($attributes, Closure $callback)
		{
			static ::$group = $attributes;
			call_user_func($callback);
			static ::$group = null;
		}
		public static function register($method, $route, $action)
		{
			if (ctype_digit($route)) $route = "({$route})";
			if (is_string($route)) $route = explode(', ', $route);
			if (is_array($method)) {
				foreach ($method as $http) {
					static ::register($http, $route, $action);
				}
				return;
			}
			foreach ((array)$route as $uri) {
				if ($method == '*') {
					foreach (static ::$methods as $method) {
						static ::register($method, $route, $action);
					}
					continue;
				}
				$uri = ltrim(str_replace('(:bundle)', static ::$bundle, $uri), '/');
				if ($uri == '') {
					$uri = '/';
				}
				if ($uri[0] == '(') {
					$routes = & static ::$fallback;
				} else {
					$routes = & static ::$routes;
				}
				if (is_array($action)) {
					$routes[$method][$uri] = $action;
				} else {
					$routes[$method][$uri] = static ::action($action);
				}
				if (!is_null(static ::$group)) {
					$routes[$method][$uri]+= static ::$group;
				}
				if (!isset($routes[$method][$uri]['https'])) {
					$routes[$method][$uri]['https'] = false;
				}
			}
		}
		protected static function action($action)
		{
			if (is_string($action)) {
				$action = array('uses' => $action);
			} elseif ($action instanceof Closure) {
				$action = array($action);
			}
			return (array)$action;
		}
		public static function secure_controller($controllers, $defaults = 'index')
		{
			static ::controller($controllers, $defaults, true);
		}
		public static function controller($controllers, $defaults = 'index', $https = null)
		{
			foreach ((array)$controllers as $identifier) {
				list($bundle, $controller) = Bundle::parse($identifier);
				$controller = str_replace('.', '/', $controller);
				$root = Bundle::option($bundle, 'handles');
				if (ends_with($controller, 'home')) {
					static ::root($identifier, $controller, $root);
				}
				$wildcards = static ::repeat('(:any?)', static ::$segments);
				$pattern = trim("{$root}/{$controller}/{$wildcards}", '/');
				$uses = "{$identifier}@(:1)";
				$attributes = compact('uses', 'defaults', 'https');
				static ::register('*', $pattern, $attributes);
			}
		}
		protected static function root($identifier, $controller, $root)
		{
			if ($controller !== 'home') {
				$home = dirname($controller);
			} else {
				$home = '';
			}
			$pattern = trim($root . '/' . $home, '/') ? : '/';
			$attributes = array('uses' => "{$identifier}@index");
			static ::register('*', $pattern, $attributes);
		}
		public static function find($name)
		{
			if (isset(static ::$names[$name])) return static ::$names[$name];
			if (count(static ::$names) == 0) {
				foreach (Bundle::names() as $bundle) {
					Bundle::routes($bundle);
				}
			}
			foreach (static ::routes() as $method => $routes) {
				foreach ($routes as $key => $value) {
					if (isset($value['as']) and $value['as'] === $name) {
						return static ::$names[$name] = array($key => $value);
					}
				}
			}
		}
		public static function uses($action)
		{
			if (isset(static ::$uses[$action])) {
				return static ::$uses[$action];
			}
			Bundle::routes(Bundle::name($action));
			foreach (static ::routes() as $method => $routes) {
				foreach ($routes as $key => $value) {
					if (isset($value['uses']) and $value['uses'] === $action) {
						return static ::$uses[$action] = array($key => $value);
					}
				}
			}
		}
		public static function route($method, $uri)
		{
			Bundle::start($bundle = Bundle::handles($uri));
			$routes = (array)static ::method($method);
			if (array_key_exists($uri, $routes)) {
				$action = $routes[$uri];
				return new Route($method, $uri, $action);
			}
			if (!is_null($route = static ::match($method, $uri))) {
				return $route;
			}
		}
		protected static function match($method, $uri)
		{
			foreach (static ::method($method) as $route => $action) {
				if (str_contains($route, '(')) {
					$pattern = '#^' . static ::wildcards($route) . '$#u';
					if (preg_match($pattern, $uri, $parameters)) {
						return new Route($method, $route, $action, array_slice($parameters, 1));
					}
				}
			}
		}
		protected static function wildcards($key)
		{
			list($search, $replace) = array_divide(static ::$optional);
			$key = str_replace($search, $replace, $key, $count);
			if ($count > 0) {
				$key.= str_repeat(')?', $count);
			}
			return strtr($key, static ::$patterns);
		}
		public static function routes()
		{
			$routes = static ::$routes;
			foreach (static ::$methods as $method) {
				if (!isset($routes[$method])) $routes[$method] = array();
				$fallback = array_get(static ::$fallback, $method, array());
				$routes[$method] = array_merge($routes[$method], $fallback);
			}
			return $routes;
		}
		public static function method($method)
		{
			$routes = array_get(static ::$routes, $method, array());
			return array_merge($routes, array_get(static ::$fallback, $method, array()));
		}
		public static function patterns()
		{
			return array_merge(static ::$patterns, static ::$optional);
		}
		protected static function repeat($pattern, $times)
		{
			return implode('/', array_fill(0, $times, $pattern));
		}
	}
}
namespace Symfony\Component\HttpFoundation {
	use Symfony\Component\HttpFoundation\File\UploadedFile;
	use Symfony\Component\HttpFoundation\Session\SessionInterface;
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\ParameterBag.php
	 */
	class ParameterBag implements \IteratorAggregate, \Countable
	{
		protected $parameters;
		public function __construct(array $parameters = array())
		{
			$this->parameters = $parameters;
		}
		public function all()
		{
			return $this->parameters;
		}
		public function keys()
		{
			return array_keys($this->parameters);
		}
		public function replace(array $parameters = array())
		{
			$this->parameters = $parameters;
		}
		public function add(array $parameters = array())
		{
			$this->parameters = array_replace($this->parameters, $parameters);
		}
		public function get($path, $default = null, $deep = false)
		{
			if (!$deep || false === $pos = strpos($path, '[')) {
				return array_key_exists($path, $this->parameters) ? $this->parameters[$path] : $default;
			}
			$root = substr($path, 0, $pos);
			if (!array_key_exists($root, $this->parameters)) {
				return $default;
			}
			$value = $this->parameters[$root];
			$currentKey = null;
			for ($i = $pos, $c = strlen($path); $i < $c; $i++) {
				$char = $path[$i];
				if ('[' === $char) {
					if (null !== $currentKey) {
						throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "[" at position %d.', $i));
					}
					$currentKey = '';
				} elseif (']' === $char) {
					if (null === $currentKey) {
						throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "]" at position %d.', $i));
					}
					if (!is_array($value) || !array_key_exists($currentKey, $value)) {
						return $default;
					}
					$value = $value[$currentKey];
					$currentKey = null;
				} else {
					if (null === $currentKey) {
						throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "%s" at position %d.', $char, $i));
					}
					$currentKey.= $char;
				}
			}
			if (null !== $currentKey) {
				throw new \InvalidArgumentException(sprintf('Malformed path. Path must end with "]".'));
			}
			return $value;
		}
		public function set($key, $value)
		{
			$this->parameters[$key] = $value;
		}
		public function has($key)
		{
			return array_key_exists($key, $this->parameters);
		}
		public function remove($key)
		{
			unset($this->parameters[$key]);
		}
		public function getAlpha($key, $default = '', $deep = false)
		{
			return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default, $deep));
		}
		public function getAlnum($key, $default = '', $deep = false)
		{
			return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default, $deep));
		}
		public function getDigits($key, $default = '', $deep = false)
		{
			return str_replace(array('-', '+'), '', $this->filter($key, $default, $deep, FILTER_SANITIZE_NUMBER_INT));
		}
		public function getInt($key, $default = 0, $deep = false)
		{
			return (int)$this->get($key, $default, $deep);
		}
		public function filter($key, $default = null, $deep = false, $filter = FILTER_DEFAULT, $options = array())
		{
			$value = $this->get($key, $default, $deep);
			if (!is_array($options) && $options) {
				$options = array('flags' => $options);
			}
			if (is_array($value) && !isset($options['flags'])) {
				$options['flags'] = FILTER_REQUIRE_ARRAY;
			}
			return filter_var($value, $filter, $options);
		}
		public function getIterator()
		{
			return new \ArrayIterator($this->parameters);
		}
		public function count()
		{
			return count($this->parameters);
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\ApacheRequest.php
	 */
	class ApacheRequest extends Request
	{
		protected function prepareRequestUri()
		{
			return $this->server->get('REQUEST_URI');
		}
		protected function prepareBaseUrl()
		{
			$baseUrl = $this->server->get('SCRIPT_NAME');
			if (false === strpos($this->server->get('REQUEST_URI'), $baseUrl)) {
				return rtrim(dirname($baseUrl), '/\\');
			}
			return $baseUrl;
		}
		protected function preparePathInfo()
		{
			return $this->server->get('PATH_INFO') ? : substr($this->prepareRequestUri(), strlen($this->prepareBaseUrl())) ? : '/';
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Cookie.php
	 */
	class Cookie
	{
		protected $name;
		protected $value;
		protected $domain;
		protected $expire;
		protected $path;
		protected $secure;
		protected $httpOnly;
		public function __construct($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
		{
			if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
				throw new \InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
			}
			if (empty($name)) {
				throw new \InvalidArgumentException('The cookie name cannot be empty.');
			}
			if ($expire instanceof \DateTime) {
				$expire = $expire->format('U');
			} elseif (!is_numeric($expire)) {
				$expire = strtotime($expire);
				if (false === $expire || - 1 === $expire) {
					throw new \InvalidArgumentException('The cookie expiration time is not valid.');
				}
			}
			$this->name = $name;
			$this->value = $value;
			$this->domain = $domain;
			$this->expire = $expire;
			$this->path = empty($path) ? '/' : $path;
			$this->secure = (Boolean)$secure;
			$this->httpOnly = (Boolean)$httpOnly;
		}
		public function __toString()
		{
			$str = urlencode($this->getName()) . '=';
			if ('' === (string)$this->getValue()) {
				$str.= 'deleted; expires=' . gmdate("D, d-M-Y H:i:s T", time() - 31536001);
			} else {
				$str.= urlencode($this->getValue());
				if ($this->getExpiresTime() !== 0) {
					$str.= '; expires=' . gmdate("D, d-M-Y H:i:s T", $this->getExpiresTime());
				}
			}
			if ('/' !== $this->path) {
				$str.= '; path=' . $this->path;
			}
			if (null !== $this->getDomain()) {
				$str.= '; domain=' . $this->getDomain();
			}
			if (true === $this->isSecure()) {
				$str.= '; secure';
			}
			if (true === $this->isHttpOnly()) {
				$str.= '; httponly';
			}
			return $str;
		}
		public function getName()
		{
			return $this->name;
		}
		public function getValue()
		{
			return $this->value;
		}
		public function getDomain()
		{
			return $this->domain;
		}
		public function getExpiresTime()
		{
			return $this->expire;
		}
		public function getPath()
		{
			return $this->path;
		}
		public function isSecure()
		{
			return $this->secure;
		}
		public function isHttpOnly()
		{
			return $this->httpOnly;
		}
		public function isCleared()
		{
			return $this->expire < time();
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\FileBag.php
	 */
	class FileBag extends ParameterBag
	{
		private static $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');
		public function __construct(array $parameters = array())
		{
			$this->replace($parameters);
		}
		public function replace(array $files = array())
		{
			$this->parameters = array();
			$this->add($files);
		}
		public function set($key, $value)
		{
			if (!is_array($value) && !$value instanceof UploadedFile) {
				throw new \InvalidArgumentException('An uploaded file must be an array or an instance of UploadedFile.');
			}
			parent::set($key, $this->convertFileInformation($value));
		}
		public function add(array $files = array())
		{
			foreach ($files as $key => $file) {
				$this->set($key, $file);
			}
		}
		protected function convertFileInformation($file)
		{
			if ($file instanceof UploadedFile) {
				return $file;
			}
			$file = $this->fixPhpFilesArray($file);
			if (is_array($file)) {
				$keys = array_keys($file);
				sort($keys);
				if ($keys == self::$fileKeys) {
					if (UPLOAD_ERR_NO_FILE == $file['error']) {
						$file = null;
					} else {
						$file = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']);
					}
				} else {
					$file = array_map(array($this, 'convertFileInformation'), $file);
				}
			}
			return $file;
		}
		protected function fixPhpFilesArray($data)
		{
			if (!is_array($data)) {
				return $data;
			}
			$keys = array_keys($data);
			sort($keys);
			if (self::$fileKeys != $keys || !isset($data['name']) || !is_array($data['name'])) {
				return $data;
			}
			$files = $data;
			foreach (self::$fileKeys as $k) {
				unset($files[$k]);
			}
			foreach (array_keys($data['name']) as $key) {
				$files[$key] = $this->fixPhpFilesArray(array('error' => $data['error'][$key], 'name' => $data['name'][$key], 'type' => $data['type'][$key], 'tmp_name' => $data['tmp_name'][$key], 'size' => $data['size'][$key]));
			}
			return $files;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\HeaderBag.php
	 */
	class HeaderBag implements \IteratorAggregate, \Countable
	{
		protected $headers;
		protected $cacheControl;
		public function __construct(array $headers = array())
		{
			$this->cacheControl = array();
			$this->headers = array();
			foreach ($headers as $key => $values) {
				$this->set($key, $values);
			}
		}
		public function __toString()
		{
			if (!$this->headers) {
				return '';
			}
			$max = max(array_map('strlen', array_keys($this->headers))) + 1;
			$content = '';
			ksort($this->headers);
			foreach ($this->headers as $name => $values) {
				$name = implode('-', array_map('ucfirst', explode('-', $name)));
				foreach ($values as $value) {
					$content.= sprintf("%-{$max}s %s\r\n", $name . ':', $value);
				}
			}
			return $content;
		}
		public function all()
		{
			return $this->headers;
		}
		public function keys()
		{
			return array_keys($this->headers);
		}
		public function replace(array $headers = array())
		{
			$this->headers = array();
			$this->add($headers);
		}
		public function add(array $headers)
		{
			foreach ($headers as $key => $values) {
				$this->set($key, $values);
			}
		}
		public function get($key, $default = null, $first = true)
		{
			$key = strtr(strtolower($key), '_', '-');
			if (!array_key_exists($key, $this->headers)) {
				if (null === $default) {
					return $first ? null : array();
				}
				return $first ? $default : array($default);
			}
			if ($first) {
				return count($this->headers[$key]) ? $this->headers[$key][0] : $default;
			}
			return $this->headers[$key];
		}
		public function set($key, $values, $replace = true)
		{
			$key = strtr(strtolower($key), '_', '-');
			$values = array_values((array)$values);
			if (true === $replace || !isset($this->headers[$key])) {
				$this->headers[$key] = $values;
			} else {
				$this->headers[$key] = array_merge($this->headers[$key], $values);
			}
			if ('cache-control' === $key) {
				$this->cacheControl = $this->parseCacheControl($values[0]);
			}
		}
		public function has($key)
		{
			return array_key_exists(strtr(strtolower($key), '_', '-'), $this->headers);
		}
		public function contains($key, $value)
		{
			return in_array($value, $this->get($key, null, false));
		}
		public function remove($key)
		{
			$key = strtr(strtolower($key), '_', '-');
			unset($this->headers[$key]);
			if ('cache-control' === $key) {
				$this->cacheControl = array();
			}
		}
		public function getDate($key, \DateTime $default = null)
		{
			if (null === $value = $this->get($key)) {
				return $default;
			}
			if (false === $date = \DateTime::createFromFormat(DATE_RFC2822, $value)) {
				throw new \RuntimeException(sprintf('The %s HTTP header is not parseable (%s).', $key, $value));
			}
			return $date;
		}
		public function addCacheControlDirective($key, $value = true)
		{
			$this->cacheControl[$key] = $value;
			$this->set('Cache-Control', $this->getCacheControlHeader());
		}
		public function hasCacheControlDirective($key)
		{
			return array_key_exists($key, $this->cacheControl);
		}
		public function getCacheControlDirective($key)
		{
			return array_key_exists($key, $this->cacheControl) ? $this->cacheControl[$key] : null;
		}
		public function removeCacheControlDirective($key)
		{
			unset($this->cacheControl[$key]);
			$this->set('Cache-Control', $this->getCacheControlHeader());
		}
		public function getIterator()
		{
			return new \ArrayIterator($this->headers);
		}
		public function count()
		{
			return count($this->headers);
		}
		protected function getCacheControlHeader()
		{
			$parts = array();
			ksort($this->cacheControl);
			foreach ($this->cacheControl as $key => $value) {
				if (true === $value) {
					$parts[] = $key;
				} else {
					if (preg_match('#[^a-zA-Z0-9._-]#', $value)) {
						$value = '"' . $value . '"';
					}
					$parts[] = "$key=$value";
				}
			}
			return implode(', ', $parts);
		}
		protected function parseCacheControl($header)
		{
			$cacheControl = array();
			preg_match_all('#([a-zA-Z][a-zA-Z_-]*)\s*(?:=(?:"([^"]*)"|([^ \t",;]*)))?#', $header, $matches, PREG_SET_ORDER);
			foreach ($matches as $match) {
				$cacheControl[strtolower($match[1]) ] = isset($match[3]) ? $match[3] : (isset($match[2]) ? $match[2] : true);
			}
			return $cacheControl;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\JsonResponse.php
	 */
	class JsonResponse extends Response
	{
		protected $data;
		protected $callback;
		public function __construct($data = array(), $status = 200, $headers = array())
		{
			parent::__construct('', $status, $headers);
			$this->setData($data);
		}
		public static function create($data = array(), $status = 200, $headers = array())
		{
			return new static ($data, $status, $headers);
		}
		public function setCallback($callback = null)
		{
			if (null !== $callback) {
				$pattern = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';
				$parts = explode('.', $callback);
				foreach ($parts as $part) {
					if (!preg_match($pattern, $part)) {
						throw new \InvalidArgumentException('The callback name is not valid.');
					}
				}
			}
			$this->callback = $callback;
			return $this->update();
		}
		public function setData($data = array())
		{
			if (is_array($data) && 0 === count($data)) {
				$data = new \ArrayObject();
			}
			$this->data = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
			return $this->update();
		}
		protected function update()
		{
			if (null !== $this->callback) {
				$this->headers->set('Content-Type', 'text/javascript');
				return $this->setContent(sprintf('%s(%s);', $this->callback, $this->data));
			}
			if (!$this->headers->has('Content-Type') || 'text/javascript' === $this->headers->get('Content-Type')) {
				$this->headers->set('Content-Type', 'application/json');
			}
			return $this->setContent($this->data);
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\LaravelRequest.php
	 */
	class LaravelRequest extends Request
	{
		static public function createFromGlobals()
		{
			$request = new static ($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);
			if ((0 === strpos($request->server->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded') || (0 === strpos($request->server->get('HTTP_CONTENT_TYPE'), 'application/x-www-form-urlencoded'))) && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))) {
				parse_str($request->getContent(), $data);
				if (magic_quotes()) $data = array_strip_slashes($data);
				$request->request = new ParameterBag($data);
			}
			return $request;
		}
		public function getRootUrl()
		{
			return $this->getScheme() . '://' . $this->getHttpHost() . $this->getBasePath();
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\LaravelResponse.php
	 */
	class LaravelResponse extends Response
	{
		public function send()
		{
			$this->sendHeaders();
			$this->sendContent();
			return $this;
		}
		public function finish()
		{
			if (function_exists('fastcgi_finish_request')) {
				fastcgi_finish_request();
			}
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\RedirectResponse.php
	 */
	class RedirectResponse extends Response
	{
		protected $targetUrl;
		public function __construct($url, $status = 302, $headers = array())
		{
			if (empty($url)) {
				throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
			}
			parent::__construct('', $status, $headers);
			$this->setTargetUrl($url);
			if (!$this->isRedirect()) {
				throw new \InvalidArgumentException(sprintf('The HTTP status code is not a redirect ("%s" given).', $status));
			}
		}
		public static function create($url = '', $status = 302, $headers = array())
		{
			return new static ($url, $status, $headers);
		}
		public function getTargetUrl()
		{
			return $this->targetUrl;
		}
		public function setTargetUrl($url)
		{
			if (empty($url)) {
				throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
			}
			$this->targetUrl = $url;
			$this->setContent(sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="refresh" content="1;url=%1$s" />

        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')));
			$this->headers->set('Location', $url);
			return $this;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Request.php
	 */
	class Request
	{
		const HEADER_CLIENT_IP = 'client_ip';
		const HEADER_CLIENT_HOST = 'client_host';
		const HEADER_CLIENT_PROTO = 'client_proto';
		const HEADER_CLIENT_PORT = 'client_port';
		protected static $trustProxy = false;
		protected static $trustedProxies = array();
		protected static $trustedHeaders = array(self::HEADER_CLIENT_IP => 'X_FORWARDED_FOR', self::HEADER_CLIENT_HOST => 'X_FORWARDED_HOST', self::HEADER_CLIENT_PROTO => 'X_FORWARDED_PROTO', self::HEADER_CLIENT_PORT => 'X_FORWARDED_PORT',);
		public $attributes;
		public $request;
		public $query;
		public $server;
		public $files;
		public $cookies;
		public $headers;
		protected $content;
		protected $languages;
		protected $charsets;
		protected $acceptableContentTypes;
		protected $pathInfo;
		protected $requestUri;
		protected $baseUrl;
		protected $basePath;
		protected $method;
		protected $format;
		protected $session;
		protected $locale;
		protected $defaultLocale = 'en';
		protected static $formats;
		public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
		{
			$this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
		}
		public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
		{
			$this->request = new ParameterBag($request);
			$this->query = new ParameterBag($query);
			$this->attributes = new ParameterBag($attributes);
			$this->cookies = new ParameterBag($cookies);
			$this->files = new FileBag($files);
			$this->server = new ServerBag($server);
			$this->headers = new HeaderBag($this->server->getHeaders());
			$this->content = $content;
			$this->languages = null;
			$this->charsets = null;
			$this->acceptableContentTypes = null;
			$this->pathInfo = null;
			$this->requestUri = null;
			$this->baseUrl = null;
			$this->basePath = null;
			$this->method = null;
			$this->format = null;
		}
		public static function createFromGlobals()
		{
			$request = new static ($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);
			if (0 === strpos($request->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded') && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))) {
				parse_str($request->getContent(), $data);
				$request->request = new ParameterBag($data);
			}
			return $request;
		}
		public static function create($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null)
		{
			$defaults = array('SERVER_NAME' => 'localhost', 'SERVER_PORT' => 80, 'HTTP_HOST' => 'localhost', 'HTTP_USER_AGENT' => 'Symfony/2.X', 'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5', 'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7', 'REMOTE_ADDR' => '127.0.0.1', 'SCRIPT_NAME' => '', 'SCRIPT_FILENAME' => '', 'SERVER_PROTOCOL' => 'HTTP/1.1', 'REQUEST_TIME' => time(),);
			$components = parse_url($uri);
			if (isset($components['host'])) {
				$defaults['SERVER_NAME'] = $components['host'];
				$defaults['HTTP_HOST'] = $components['host'];
			}
			if (isset($components['scheme'])) {
				if ('https' === $components['scheme']) {
					$defaults['HTTPS'] = 'on';
					$defaults['SERVER_PORT'] = 443;
				}
			}
			if (isset($components['port'])) {
				$defaults['SERVER_PORT'] = $components['port'];
				$defaults['HTTP_HOST'] = $defaults['HTTP_HOST'] . ':' . $components['port'];
			}
			if (isset($components['user'])) {
				$defaults['PHP_AUTH_USER'] = $components['user'];
			}
			if (isset($components['pass'])) {
				$defaults['PHP_AUTH_PW'] = $components['pass'];
			}
			if (!isset($components['path'])) {
				$components['path'] = '/';
			}
			switch (strtoupper($method)) {
			case 'POST':
			case 'PUT':
			case 'DELETE':
				$defaults['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
			case 'PATCH':
				$request = $parameters;
				$query = array();
				break;

			default:
				$request = array();
				$query = $parameters;
				break;
			}
			if (isset($components['query'])) {
				parse_str(html_entity_decode($components['query']), $qs);
				$query = array_replace($qs, $query);
			}
			$queryString = http_build_query($query, '', '&');
			$uri = $components['path'] . ('' !== $queryString ? '?' . $queryString : '');
			$server = array_replace($defaults, $server, array('REQUEST_METHOD' => strtoupper($method), 'PATH_INFO' => '', 'REQUEST_URI' => $uri, 'QUERY_STRING' => $queryString,));
			return new static ($query, $request, array(), $cookies, $files, $server, $content);
		}
		public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null)
		{
			$dup = clone $this;
			if ($query !== null) {
				$dup->query = new ParameterBag($query);
			}
			if ($request !== null) {
				$dup->request = new ParameterBag($request);
			}
			if ($attributes !== null) {
				$dup->attributes = new ParameterBag($attributes);
			}
			if ($cookies !== null) {
				$dup->cookies = new ParameterBag($cookies);
			}
			if ($files !== null) {
				$dup->files = new FileBag($files);
			}
			if ($server !== null) {
				$dup->server = new ServerBag($server);
				$dup->headers = new HeaderBag($dup->server->getHeaders());
			}
			$dup->languages = null;
			$dup->charsets = null;
			$dup->acceptableContentTypes = null;
			$dup->pathInfo = null;
			$dup->requestUri = null;
			$dup->baseUrl = null;
			$dup->basePath = null;
			$dup->method = null;
			$dup->format = null;
			return $dup;
		}
		public function __clone()
		{
			$this->query = clone $this->query;
			$this->request = clone $this->request;
			$this->attributes = clone $this->attributes;
			$this->cookies = clone $this->cookies;
			$this->files = clone $this->files;
			$this->server = clone $this->server;
			$this->headers = clone $this->headers;
		}
		public function __toString()
		{
			return sprintf('%s %s %s', $this->getMethod(), $this->getRequestUri(), $this->server->get('SERVER_PROTOCOL')) . "\r\n" . $this->headers . "\r\n" . $this->getContent();
		}
		public function overrideGlobals()
		{
			$_GET = $this->query->all();
			$_POST = $this->request->all();
			$_SERVER = $this->server->all();
			$_COOKIE = $this->cookies->all();
			foreach ($this->headers->all() as $key => $value) {
				$key = strtoupper(str_replace('-', '_', $key));
				if (in_array($key, array('CONTENT_TYPE', 'CONTENT_LENGTH'))) {
					$_SERVER[$key] = implode(', ', $value);
				} else {
					$_SERVER['HTTP_' . $key] = implode(', ', $value);
				}
			}
			$request = array('g' => $_GET, 'p' => $_POST, 'c' => $_COOKIE);
			$requestOrder = ini_get('request_order') ? : ini_get('variable_order');
			$requestOrder = preg_replace('#[^cgp]#', '', strtolower($requestOrder)) ? : 'gp';
			$_REQUEST = array();
			foreach (str_split($requestOrder) as $order) {
				$_REQUEST = array_merge($_REQUEST, $request[$order]);
			}
		}
		public static function trustProxyData()
		{
			self::$trustProxy = true;
		}
		public static function setTrustedProxies(array $proxies)
		{
			self::$trustedProxies = $proxies;
			self::$trustProxy = $proxies ? true : false;
		}
		public static function setTrustedHeaderName($key, $value)
		{
			if (!array_key_exists($key, self::$trustedHeaders)) {
				throw new \InvalidArgumentException(sprintf('Unable to set the trusted header name for key "%s".', $key));
			}
			self::$trustedHeaders[$key] = $value;
		}
		public static function isProxyTrusted()
		{
			return self::$trustProxy;
		}
		public static function normalizeQueryString($qs)
		{
			if ('' == $qs) {
				return '';
			}
			$parts = array();
			$order = array();
			foreach (explode('&', $qs) as $param) {
				if ('' === $param || '=' === $param[0]) {
					continue;
				}
				$keyValuePair = explode('=', $param, 2);
				$parts[] = isset($keyValuePair[1]) ? rawurlencode(urldecode($keyValuePair[0])) . '=' . rawurlencode(urldecode($keyValuePair[1])) : rawurlencode(urldecode($keyValuePair[0]));
				$order[] = urldecode($keyValuePair[0]);
			}
			array_multisort($order, SORT_ASC, $parts);
			return implode('&', $parts);
		}
		public function get($key, $default = null, $deep = false)
		{
			return $this->query->get($key, $this->attributes->get($key, $this->request->get($key, $default, $deep), $deep), $deep);
		}
		public function getSession()
		{
			return $this->session;
		}
		public function hasPreviousSession()
		{
			return $this->hasSession() && $this->cookies->has($this->session->getName());
		}
		public function hasSession()
		{
			return null !== $this->session;
		}
		public function setSession(SessionInterface $session)
		{
			$this->session = $session;
		}
		public function getClientIp()
		{
			$ip = $this->server->get('REMOTE_ADDR');
			if (!self::$trustProxy) {
				return $ip;
			}
			if (!self::$trustedHeaders[self::HEADER_CLIENT_IP] || !$this->headers->has(self::$trustedHeaders[self::HEADER_CLIENT_IP])) {
				return $ip;
			}
			$clientIps = array_map('trim', explode(',', $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_IP])));
			$clientIps[] = $ip;
			$trustedProxies = self::$trustProxy && !self::$trustedProxies ? array($ip) : self::$trustedProxies;
			$clientIps = array_diff($clientIps, $trustedProxies);
			return array_pop($clientIps);
		}
		public function getScriptName()
		{
			return $this->server->get('SCRIPT_NAME', $this->server->get('ORIG_SCRIPT_NAME', ''));
		}
		public function getPathInfo()
		{
			if (null === $this->pathInfo) {
				$this->pathInfo = $this->preparePathInfo();
			}
			return $this->pathInfo;
		}
		public function getBasePath()
		{
			if (null === $this->basePath) {
				$this->basePath = $this->prepareBasePath();
			}
			return $this->basePath;
		}
		public function getBaseUrl()
		{
			if (null === $this->baseUrl) {
				$this->baseUrl = $this->prepareBaseUrl();
			}
			return $this->baseUrl;
		}
		public function getScheme()
		{
			return $this->isSecure() ? 'https' : 'http';
		}
		public function getPort()
		{
			if (self::$trustProxy && self::$trustedHeaders[self::HEADER_CLIENT_PORT] && $port = $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_PORT])) {
				return $port;
			}
			return $this->server->get('SERVER_PORT');
		}
		public function getUser()
		{
			return $this->server->get('PHP_AUTH_USER');
		}
		public function getPassword()
		{
			return $this->server->get('PHP_AUTH_PW');
		}
		public function getUserInfo()
		{
			$userinfo = $this->getUser();
			$pass = $this->getPassword();
			if ('' != $pass) {
				$userinfo.= ":$pass";
			}
			return $userinfo;
		}
		public function getHttpHost()
		{
			$scheme = $this->getScheme();
			$port = $this->getPort();
			if (('http' == $scheme && $port == 80) || ('https' == $scheme && $port == 443)) {
				return $this->getHost();
			}
			return $this->getHost() . ':' . $port;
		}
		public function getRequestUri()
		{
			if (null === $this->requestUri) {
				$this->requestUri = $this->prepareRequestUri();
			}
			return $this->requestUri;
		}
		public function getSchemeAndHttpHost()
		{
			return $this->getScheme() . '://' . $this->getHttpHost();
		}
		public function getUri()
		{
			$qs = $this->getQueryString();
			if (null !== $qs) {
				$qs = '?' . $qs;
			}
			return $this->getSchemeAndHttpHost() . $this->getBaseUrl() . $this->getPathInfo() . $qs;
		}
		public function getUriForPath($path)
		{
			return $this->getSchemeAndHttpHost() . $this->getBaseUrl() . $path;
		}
		public function getQueryString()
		{
			$qs = static ::normalizeQueryString($this->server->get('QUERY_STRING'));
			return '' === $qs ? null : $qs;
		}
		public function isSecure()
		{
			if (self::$trustProxy && self::$trustedHeaders[self::HEADER_CLIENT_PROTO] && $proto = $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_PROTO])) {
				return in_array(strtolower($proto), array('https', 'on', '1'));
			}
			return 'on' == strtolower($this->server->get('HTTPS')) || 1 == $this->server->get('HTTPS');
		}
		public function getHost()
		{
			if (self::$trustProxy && self::$trustedHeaders[self::HEADER_CLIENT_HOST] && $host = $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_HOST])) {
				$elements = explode(',', $host);
				$host = $elements[count($elements) - 1];
			} elseif (!$host = $this->headers->get('HOST')) {
				if (!$host = $this->server->get('SERVER_NAME')) {
					$host = $this->server->get('SERVER_ADDR', '');
				}
			}
			$host = strtolower(preg_replace('/:\d+$/', '', trim($host)));
			if ($host && !preg_match('/^\[?(?:[a-zA-Z0-9-:\]_]+\.?)+$/', $host)) {
				throw new \UnexpectedValueException('Invalid Host');
			}
			return $host;
		}
		public function setMethod($method)
		{
			$this->method = null;
			$this->server->set('REQUEST_METHOD', $method);
		}
		public function getMethod()
		{
			if (null === $this->method) {
				$this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
				if ('POST' === $this->method) {
					$this->method = strtoupper($this->headers->get('X-HTTP-METHOD-OVERRIDE', $this->request->get('_method', $this->query->get('_method', 'POST'))));
				}
			}
			return $this->method;
		}
		public function getMimeType($format)
		{
			if (null === static ::$formats) {
				static ::initializeFormats();
			}
			return isset(static ::$formats[$format]) ? static ::$formats[$format][0] : null;
		}
		public function getFormat($mimeType)
		{
			if (false !== $pos = strpos($mimeType, ';')) {
				$mimeType = substr($mimeType, 0, $pos);
			}
			if (null === static ::$formats) {
				static ::initializeFormats();
			}
			foreach (static ::$formats as $format => $mimeTypes) {
				if (in_array($mimeType, (array)$mimeTypes)) {
					return $format;
				}
			}
			return null;
		}
		public function setFormat($format, $mimeTypes)
		{
			if (null === static ::$formats) {
				static ::initializeFormats();
			}
			static ::$formats[$format] = is_array($mimeTypes) ? $mimeTypes : array($mimeTypes);
		}
		public function getRequestFormat($default = 'html')
		{
			if (null === $this->format) {
				$this->format = $this->get('_format', $default);
			}
			return $this->format;
		}
		public function setRequestFormat($format)
		{
			$this->format = $format;
		}
		public function getContentType()
		{
			return $this->getFormat($this->headers->get('CONTENT_TYPE'));
		}
		public function setDefaultLocale($locale)
		{
			$this->defaultLocale = $locale;
			if (null === $this->locale) {
				$this->setPhpDefaultLocale($locale);
			}
		}
		public function setLocale($locale)
		{
			$this->setPhpDefaultLocale($this->locale = $locale);
		}
		public function getLocale()
		{
			return null === $this->locale ? $this->defaultLocale : $this->locale;
		}
		public function isMethod($method)
		{
			return $this->getMethod() === strtoupper($method);
		}
		public function isMethodSafe()
		{
			return in_array($this->getMethod(), array('GET', 'HEAD'));
		}
		public function getContent($asResource = false)
		{
			if (false === $this->content || (true === $asResource && null !== $this->content)) {
				throw new \LogicException('getContent() can only be called once when using the resource return type.');
			}
			if (true === $asResource) {
				$this->content = false;
				return fopen('php://input', 'rb');
			}
			if (null === $this->content) {
				$this->content = file_get_contents('php://input');
			}
			return $this->content;
		}
		public function getETags()
		{
			return preg_split('/\s*,\s*/', $this->headers->get('if_none_match'), null, PREG_SPLIT_NO_EMPTY);
		}
		public function isNoCache()
		{
			return $this->headers->hasCacheControlDirective('no-cache') || 'no-cache' == $this->headers->get('Pragma');
		}
		public function getPreferredLanguage(array $locales = null)
		{
			$preferredLanguages = $this->getLanguages();
			if (empty($locales)) {
				return isset($preferredLanguages[0]) ? $preferredLanguages[0] : null;
			}
			if (!$preferredLanguages) {
				return $locales[0];
			}
			$preferredLanguages = array_values(array_intersect($preferredLanguages, $locales));
			return isset($preferredLanguages[0]) ? $preferredLanguages[0] : $locales[0];
		}
		public function getLanguages()
		{
			if (null !== $this->languages) {
				return $this->languages;
			}
			$languages = $this->splitHttpAcceptHeader($this->headers->get('Accept-Language'));
			$this->languages = array();
			foreach ($languages as $lang => $q) {
				if (strstr($lang, '-')) {
					$codes = explode('-', $lang);
					if ($codes[0] == 'i') {
						if (count($codes) > 1) {
							$lang = $codes[1];
						}
					} else {
						for ($i = 0, $max = count($codes); $i < $max; $i++) {
							if ($i == 0) {
								$lang = strtolower($codes[0]);
							} else {
								$lang.= '_' . strtoupper($codes[$i]);
							}
						}
					}
				}
				$this->languages[] = $lang;
			}
			return $this->languages;
		}
		public function getCharsets()
		{
			if (null !== $this->charsets) {
				return $this->charsets;
			}
			return $this->charsets = array_keys($this->splitHttpAcceptHeader($this->headers->get('Accept-Charset')));
		}
		public function getAcceptableContentTypes()
		{
			if (null !== $this->acceptableContentTypes) {
				return $this->acceptableContentTypes;
			}
			return $this->acceptableContentTypes = array_keys($this->splitHttpAcceptHeader($this->headers->get('Accept')));
		}
		public function isXmlHttpRequest()
		{
			return 'XMLHttpRequest' == $this->headers->get('X-Requested-With');
		}
		public function splitHttpAcceptHeader($header)
		{
			if (!$header) {
				return array();
			}
			$values = array();
			$groups = array();
			foreach (array_filter(explode(',', $header)) as $value) {
				if (preg_match('/;\s*(q=.*$)/', $value, $match)) {
					$q = substr(trim($match[1]), 2);
					$value = trim(substr($value, 0, -strlen($match[0])));
				} else {
					$q = 1;
				}
				$groups[$q][] = $value;
			}
			krsort($groups);
			foreach ($groups as $q => $items) {
				$q = (float)$q;
				if (0 < $q) {
					foreach ($items as $value) {
						$values[trim($value) ] = $q;
					}
				}
			}
			return $values;
		}
		protected function prepareRequestUri()
		{
			$requestUri = '';
			if ($this->headers->has('X_ORIGINAL_URL') && false !== stripos(PHP_OS, 'WIN')) {
				$requestUri = $this->headers->get('X_ORIGINAL_URL');
			} elseif ($this->headers->has('X_REWRITE_URL') && false !== stripos(PHP_OS, 'WIN')) {
				$requestUri = $this->headers->get('X_REWRITE_URL');
			} elseif ($this->server->get('IIS_WasUrlRewritten') == '1' && $this->server->get('UNENCODED_URL') != '') {
				$requestUri = $this->server->get('UNENCODED_URL');
			} elseif ($this->server->has('REQUEST_URI')) {
				$requestUri = $this->server->get('REQUEST_URI');
				$schemeAndHttpHost = $this->getSchemeAndHttpHost();
				if (strpos($requestUri, $schemeAndHttpHost) === 0) {
					$requestUri = substr($requestUri, strlen($schemeAndHttpHost));
				}
			} elseif ($this->server->has('ORIG_PATH_INFO')) {
				$requestUri = $this->server->get('ORIG_PATH_INFO');
				if ('' != $this->server->get('QUERY_STRING')) {
					$requestUri.= '?' . $this->server->get('QUERY_STRING');
				}
			}
			return $requestUri;
		}
		protected function prepareBaseUrl()
		{
			$filename = basename($this->server->get('SCRIPT_FILENAME'));
			if (basename($this->server->get('SCRIPT_NAME')) === $filename) {
				$baseUrl = $this->server->get('SCRIPT_NAME');
			} elseif (basename($this->server->get('PHP_SELF')) === $filename) {
				$baseUrl = $this->server->get('PHP_SELF');
			} elseif (basename($this->server->get('ORIG_SCRIPT_NAME')) === $filename) {
				$baseUrl = $this->server->get('ORIG_SCRIPT_NAME');
			} else {
				$path = $this->server->get('PHP_SELF', '');
				$file = $this->server->get('SCRIPT_FILENAME', '');
				$segs = explode('/', trim($file, '/'));
				$segs = array_reverse($segs);
				$index = 0;
				$last = count($segs);
				$baseUrl = '';
				do {
					$seg = $segs[$index];
					$baseUrl = '/' . $seg . $baseUrl;
					++$index;
				} while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
			}
			$requestUri = $this->getRequestUri();
			if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, $baseUrl)) {
				return $prefix;
			}
			if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, dirname($baseUrl))) {
				return rtrim($prefix, '/');
			}
			$truncatedRequestUri = $requestUri;
			if (($pos = strpos($requestUri, '?')) !== false) {
				$truncatedRequestUri = substr($requestUri, 0, $pos);
			}
			$basename = basename($baseUrl);
			if (empty($basename) || !strpos(rawurldecode($truncatedRequestUri), $basename)) {
				return '';
			}
			if ((strlen($requestUri) >= strlen($baseUrl)) && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0))) {
				$baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
			}
			return rtrim($baseUrl, '/');
		} protected function prepareBasePath()
		{
			$filename = basename($this->server->get('SCRIPT_FILENAME'));
			$baseUrl = $this->getBaseUrl();
			if (empty($baseUrl)) {
				return '';
			}
			if (basename($baseUrl) === $filename) {
				$basePath = dirname($baseUrl);
			} else {
				$basePath = $baseUrl;
			}
			if ('\\' === DIRECTORY_SEPARATOR) {
				$basePath = str_replace('\\', '/', $basePath);
			}
			return rtrim($basePath, '/');
		}
		protected function preparePathInfo()
		{
			$baseUrl = $this->getBaseUrl();
			if (null === ($requestUri = $this->getRequestUri())) {
				return '/';
			}
			$pathInfo = '/';
			if ($pos = strpos($requestUri, '?')) {
				$requestUri = substr($requestUri, 0, $pos);
			}
			if ((null !== $baseUrl) && (false === ($pathInfo = substr($requestUri, strlen($baseUrl))))) {
				return '/';
			} elseif (null === $baseUrl) {
				return $requestUri;
			}
			return (string)$pathInfo;
		}
		protected static function initializeFormats()
		{
			static ::$formats = array('html' => array('text/html', 'application/xhtml+xml'), 'txt' => array('text/plain'), 'js' => array('application/javascript', 'application/x-javascript', 'text/javascript'), 'css' => array('text/css'), 'json' => array('application/json', 'application/x-json'), 'xml' => array('text/xml', 'application/xml', 'application/x-xml'), 'rdf' => array('application/rdf+xml'), 'atom' => array('application/atom+xml'), 'rss' => array('application/rss+xml'),);
		}
		private function setPhpDefaultLocale($locale)
		{
			try {
				if (class_exists('Locale', false)) {
					\Locale::setDefault($locale);
				}
			}
			catch(\Exception $e) {
			}
		}
		private function getUrlencodedPrefix($string, $prefix)
		{
			if (0 !== strpos(rawurldecode($string), $prefix)) {
				return false;
			}
			$len = strlen($prefix);
			if (preg_match("#^(%[[:xdigit:]]{2}|.){{$len}}#", $string, $match)) {
				return $match[0];
			}
			return false;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\RequestMatcher.php
	 */
	class RequestMatcher implements RequestMatcherInterface
	{
		private $path;
		private $host;
		private $methods = array();
		private $ip;
		private $attributes = array();
		public function __construct($path = null, $host = null, $methods = null, $ip = null, array $attributes = array())
		{
			$this->matchPath($path);
			$this->matchHost($host);
			$this->matchMethod($methods);
			$this->matchIp($ip);
			foreach ($attributes as $k => $v) {
				$this->matchAttribute($k, $v);
			}
		}
		public function matchHost($regexp)
		{
			$this->host = $regexp;
		}
		public function matchPath($regexp)
		{
			$this->path = $regexp;
		}
		public function matchIp($ip)
		{
			$this->ip = $ip;
		}
		public function matchMethod($method)
		{
			$this->methods = array_map('strtoupper', (array)$method);
		}
		public function matchAttribute($key, $regexp)
		{
			$this->attributes[$key] = $regexp;
		}
		public function matches(Request $request)
		{
			if ($this->methods && !in_array($request->getMethod(), $this->methods)) {
				return false;
			}
			foreach ($this->attributes as $key => $pattern) {
				if (!preg_match('#' . str_replace('#', '\\#', $pattern) . '#', $request->attributes->get($key))) {
					return false;
				}
			}
			if (null !== $this->path) {
				$path = str_replace('#', '\\#', $this->path);
				if (!preg_match('#' . $path . '#', rawurldecode($request->getPathInfo()))) {
					return false;
				}
			}
			if (null !== $this->host && !preg_match('#' . str_replace('#', '\\#', $this->host) . '#i', $request->getHost())) {
				return false;
			}
			if (null !== $this->ip && !$this->checkIp($request->getClientIp(), $this->ip)) {
				return false;
			}
			return true;
		}
		protected function checkIp($requestIp, $ip)
		{
			if (false !== strpos($requestIp, ':')) {
				return $this->checkIp6($requestIp, $ip);
			} else {
				return $this->checkIp4($requestIp, $ip);
			}
		}
		protected function checkIp4($requestIp, $ip)
		{
			if (false !== strpos($ip, '/')) {
				list($address, $netmask) = explode('/', $ip, 2);
				if ($netmask < 1 || $netmask > 32) {
					return false;
				}
			} else {
				$address = $ip;
				$netmask = 32;
			}
			return 0 === substr_compare(sprintf('%032b', ip2long($requestIp)), sprintf('%032b', ip2long($address)), 0, $netmask);
		}
		protected function checkIp6($requestIp, $ip)
		{
			if (!((extension_loaded('sockets') && defined('AF_INET6')) || @inet_pton('::1'))) {
				throw new \RuntimeException('Unable to check Ipv6. Check that PHP was not compiled with option "disable-ipv6".');
			}
			if (false !== strpos($ip, '/')) {
				list($address, $netmask) = explode('/', $ip, 2);
				if ($netmask < 1 || $netmask > 128) {
					return false;
				}
			} else {
				$address = $ip;
				$netmask = 128;
			}
			$bytesAddr = unpack("n*", inet_pton($address));
			$bytesTest = unpack("n*", inet_pton($requestIp));
			for ($i = 1, $ceil = ceil($netmask / 16); $i <= $ceil; $i++) {
				$left = $netmask - 16 * ($i - 1);
				$left = ($left <= 16) ? $left : 16;
				$mask = ~ (0xffff >> $left) & 0xffff;
				if (($bytesAddr[$i] & $mask) != ($bytesTest[$i] & $mask)) {
					return false;
				}
			}
			return true;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\RequestMatcherInterface.php
	 */
	interface RequestMatcherInterface {
		public function matches(Request $request);
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Response.php
	 */
	class Response
	{
		public $headers;
		protected $content;
		protected $version;
		protected $statusCode;
		protected $statusText;
		protected $charset;
		public static $statusTexts = array(100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing', 200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-Status', 208 => 'Already Reported', 226 => 'IM Used', 300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => 'Reserved', 307 => 'Temporary Redirect', 308 => 'Permanent Redirect', 400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', 418 => 'I\'m a teapot', 422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 425 => 'Reserved for WebDAV advanced collections expired proposal', 426 => 'Upgrade Required', 428 => 'Precondition Required', 429 => 'Too Many Requests', 431 => 'Request Header Fields Too Large', 500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 506 => 'Variant Also Negotiates (Experimental)', 507 => 'Insufficient Storage', 508 => 'Loop Detected', 510 => 'Not Extended', 511 => 'Network Authentication Required',);
		public function __construct($content = '', $status = 200, $headers = array())
		{
			$this->headers = new ResponseHeaderBag($headers);
			$this->setContent($content);
			$this->setStatusCode($status);
			$this->setProtocolVersion('1.0');
			if (!$this->headers->has('Date')) {
				$this->setDate(new \DateTime(null, new \DateTimeZone('UTC')));
			}
		}
		public static function create($content = '', $status = 200, $headers = array())
		{
			return new static ($content, $status, $headers);
		}
		public function __toString()
		{
			return sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText) . "\r\n" . $this->headers . "\r\n" . $this->getContent();
		}
		public function __clone()
		{
			$this->headers = clone $this->headers;
		}
		public function prepare(Request $request)
		{
			$headers = $this->headers;
			if ($this->isInformational() || in_array($this->statusCode, array(204, 304))) {
				$this->setContent(null);
			}
			if (!$headers->has('Content-Type')) {
				$format = $request->getRequestFormat();
				if (null !== $format && $mimeType = $request->getMimeType($format)) {
					$headers->set('Content-Type', $mimeType);
				}
			}
			$charset = $this->charset ? : 'UTF-8';
			if (!$headers->has('Content-Type')) {
				$headers->set('Content-Type', 'text/html; charset=' . $charset);
			} elseif (0 === strpos($headers->get('Content-Type'), 'text/') && false === strpos($headers->get('Content-Type'), 'charset')) {
				$headers->set('Content-Type', $headers->get('Content-Type') . '; charset=' . $charset);
			}
			if ($headers->has('Transfer-Encoding')) {
				$headers->remove('Content-Length');
			}
			if ('HEAD' === $request->getMethod()) {
				$length = $headers->get('Content-Length');
				$this->setContent(null);
				if ($length) {
					$headers->set('Content-Length', $length);
				}
			}
			if ('HTTP/1.0' != $request->server->get('SERVER_PROTOCOL')) {
				$this->setProtocolVersion('1.1');
			}
			if ('1.0' == $this->getProtocolVersion() && 'no-cache' == $this->headers->get('Cache-Control')) {
				$this->headers->set('pragma', 'no-cache');
				$this->headers->set('expires', -1);
			}
			return $this;
		}
		public function sendHeaders()
		{
			if (headers_sent()) {
				return $this;
			}
			header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText));
			foreach ($this->headers->all() as $name => $values) {
				foreach ($values as $value) {
					header($name . ': ' . $value, false);
				}
			}
			foreach ($this->headers->getCookies() as $cookie) {
				setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
			}
			return $this;
		}
		public function sendContent()
		{
			echo $this->content;
			return $this;
		}
		public function send()
		{
			$this->sendHeaders();
			$this->sendContent();
			if (function_exists('fastcgi_finish_request')) {
				fastcgi_finish_request();
			} elseif ('cli' !== PHP_SAPI) {
				$previous = null;
				$obStatus = ob_get_status(1);
				while (($level = ob_get_level()) > 0 && $level !== $previous) {
					$previous = $level;
					if ($obStatus[$level - 1] && isset($obStatus[$level - 1]['del']) && $obStatus[$level - 1]['del']) {
						ob_end_flush();
					}
				}
				flush();
			}
			return $this;
		}
		public function setContent($content)
		{
			if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable(array($content, '__toString'))) {
				throw new \UnexpectedValueException('The Response content must be a string or object implementing __toString(), "' . gettype($content) . '" given.');
			}
			$this->content = (string)$content;
			return $this;
		}
		public function getContent()
		{
			return $this->content;
		}
		public function setProtocolVersion($version)
		{
			$this->version = $version;
			return $this;
		}
		public function getProtocolVersion()
		{
			return $this->version;
		}
		public function setStatusCode($code, $text = null)
		{
			$this->statusCode = $code = (int)$code;
			if ($this->isInvalid()) {
				throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
			}
			if (null === $text) {
				$this->statusText = isset(self::$statusTexts[$code]) ? self::$statusTexts[$code] : '';
				return $this;
			}
			if (false === $text) {
				$this->statusText = '';
				return $this;
			}
			$this->statusText = $text;
			return $this;
		}
		public function getStatusCode()
		{
			return $this->statusCode;
		}
		public function setCharset($charset)
		{
			$this->charset = $charset;
			return $this;
		}
		public function getCharset()
		{
			return $this->charset;
		}
		public function isCacheable()
		{
			if (!in_array($this->statusCode, array(200, 203, 300, 301, 302, 404, 410))) {
				return false;
			}
			if ($this->headers->hasCacheControlDirective('no-store') || $this->headers->getCacheControlDirective('private')) {
				return false;
			}
			return $this->isValidateable() || $this->isFresh();
		}
		public function isFresh()
		{
			return $this->getTtl() > 0;
		}
		public function isValidateable()
		{
			return $this->headers->has('Last-Modified') || $this->headers->has('ETag');
		}
		public function setPrivate()
		{
			$this->headers->removeCacheControlDirective('public');
			$this->headers->addCacheControlDirective('private');
			return $this;
		}
		public function setPublic()
		{
			$this->headers->addCacheControlDirective('public');
			$this->headers->removeCacheControlDirective('private');
			return $this;
		}
		public function mustRevalidate()
		{
			return $this->headers->hasCacheControlDirective('must-revalidate') || $this->headers->has('proxy-revalidate');
		}
		public function getDate()
		{
			return $this->headers->getDate('Date', new \DateTime());
		}
		public function setDate(\DateTime $date)
		{
			$date->setTimezone(new \DateTimeZone('UTC'));
			$this->headers->set('Date', $date->format('D, d M Y H:i:s') . ' GMT');
			return $this;
		}
		public function getAge()
		{
			if ($age = $this->headers->get('Age')) {
				return $age;
			}
			return max(time() - $this->getDate()->format('U'), 0);
		}
		public function expire()
		{
			if ($this->isFresh()) {
				$this->headers->set('Age', $this->getMaxAge());
			}
			return $this;
		}
		public function getExpires()
		{
			return $this->headers->getDate('Expires');
		}
		public function setExpires(\DateTime $date = null)
		{
			if (null === $date) {
				$this->headers->remove('Expires');
			} else {
				$date = clone $date;
				$date->setTimezone(new \DateTimeZone('UTC'));
				$this->headers->set('Expires', $date->format('D, d M Y H:i:s') . ' GMT');
			}
			return $this;
		}
		public function getMaxAge()
		{
			if ($age = $this->headers->getCacheControlDirective('s-maxage')) {
				return $age;
			}
			if ($age = $this->headers->getCacheControlDirective('max-age')) {
				return $age;
			}
			if (null !== $this->getExpires()) {
				return $this->getExpires()->format('U') - $this->getDate()->format('U');
			}
			return null;
		}
		public function setMaxAge($value)
		{
			$this->headers->addCacheControlDirective('max-age', $value);
			return $this;
		}
		public function setSharedMaxAge($value)
		{
			$this->setPublic();
			$this->headers->addCacheControlDirective('s-maxage', $value);
			return $this;
		}
		public function getTtl()
		{
			if ($maxAge = $this->getMaxAge()) {
				return $maxAge - $this->getAge();
			}
			return null;
		}
		public function setTtl($seconds)
		{
			$this->setSharedMaxAge($this->getAge() + $seconds);
			return $this;
		}
		public function setClientTtl($seconds)
		{
			$this->setMaxAge($this->getAge() + $seconds);
			return $this;
		}
		public function getLastModified()
		{
			return $this->headers->getDate('Last-Modified');
		}
		public function setLastModified(\DateTime $date = null)
		{
			if (null === $date) {
				$this->headers->remove('Last-Modified');
			} else {
				$date = clone $date;
				$date->setTimezone(new \DateTimeZone('UTC'));
				$this->headers->set('Last-Modified', $date->format('D, d M Y H:i:s') . ' GMT');
			}
			return $this;
		}
		public function getEtag()
		{
			return $this->headers->get('ETag');
		}
		public function setEtag($etag = null, $weak = false)
		{
			if (null === $etag) {
				$this->headers->remove('Etag');
			} else {
				if (0 !== strpos($etag, '"')) {
					$etag = '"' . $etag . '"';
				}
				$this->headers->set('ETag', (true === $weak ? 'W/' : '') . $etag);
			}
			return $this;
		}
		public function setCache(array $options)
		{
			if ($diff = array_diff(array_keys($options), array('etag', 'last_modified', 'max_age', 's_maxage', 'private', 'public'))) {
				throw new \InvalidArgumentException(sprintf('Response does not support the following options: "%s".', implode('", "', array_values($diff))));
			}
			if (isset($options['etag'])) {
				$this->setEtag($options['etag']);
			}
			if (isset($options['last_modified'])) {
				$this->setLastModified($options['last_modified']);
			}
			if (isset($options['max_age'])) {
				$this->setMaxAge($options['max_age']);
			}
			if (isset($options['s_maxage'])) {
				$this->setSharedMaxAge($options['s_maxage']);
			}
			if (isset($options['public'])) {
				if ($options['public']) {
					$this->setPublic();
				} else {
					$this->setPrivate();
				}
			}
			if (isset($options['private'])) {
				if ($options['private']) {
					$this->setPrivate();
				} else {
					$this->setPublic();
				}
			}
			return $this;
		}
		public function setNotModified()
		{
			$this->setStatusCode(304);
			$this->setContent(null);
			foreach (array('Allow', 'Content-Encoding', 'Content-Language', 'Content-Length', 'Content-MD5', 'Content-Type', 'Last-Modified') as $header) {
				$this->headers->remove($header);
			}
			return $this;
		}
		public function hasVary()
		{
			return (Boolean)$this->headers->get('Vary');
		}
		public function getVary()
		{
			if (!$vary = $this->headers->get('Vary')) {
				return array();
			}
			return is_array($vary) ? $vary : preg_split('/[\s,]+/', $vary);
		}
		public function setVary($headers, $replace = true)
		{
			$this->headers->set('Vary', $headers, $replace);
			return $this;
		}
		public function isNotModified(Request $request)
		{
			if (!$request->isMethodSafe()) {
				return false;
			}
			$lastModified = $request->headers->get('If-Modified-Since');
			$notModified = false;
			if ($etags = $request->getEtags()) {
				$notModified = (in_array($this->getEtag(), $etags) || in_array('*', $etags)) && (!$lastModified || $this->headers->get('Last-Modified') == $lastModified);
			} elseif ($lastModified) {
				$notModified = $lastModified == $this->headers->get('Last-Modified');
			}
			if ($notModified) {
				$this->setNotModified();
			}
			return $notModified;
		}
		public function isInvalid()
		{
			return $this->statusCode < 100 || $this->statusCode >= 600;
		}
		public function isInformational()
		{
			return $this->statusCode >= 100 && $this->statusCode < 200;
		}
		public function isSuccessful()
		{
			return $this->statusCode >= 200 && $this->statusCode < 300;
		}
		public function isRedirection()
		{
			return $this->statusCode >= 300 && $this->statusCode < 400;
		}
		public function isClientError()
		{
			return $this->statusCode >= 400 && $this->statusCode < 500;
		}
		public function isServerError()
		{
			return $this->statusCode >= 500 && $this->statusCode < 600;
		}
		public function isOk()
		{
			return 200 === $this->statusCode;
		}
		public function isForbidden()
		{
			return 403 === $this->statusCode;
		}
		public function isNotFound()
		{
			return 404 === $this->statusCode;
		}
		public function isRedirect($location = null)
		{
			return in_array($this->statusCode, array(201, 301, 302, 303, 307, 308)) && (null === $location ? : $location == $this->headers->get('Location'));
		}
		public function isEmpty()
		{
			return in_array($this->statusCode, array(201, 204, 304));
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\ResponseHeaderBag.php
	 */
	class ResponseHeaderBag extends HeaderBag
	{
		const COOKIES_FLAT = 'flat';
		const COOKIES_ARRAY = 'array';
		const DISPOSITION_ATTACHMENT = 'attachment';
		const DISPOSITION_INLINE = 'inline';
		protected $computedCacheControl = array();
		protected $cookies = array();
		public function __construct(array $headers = array())
		{
			parent::__construct($headers);
			if (!isset($this->headers['cache-control'])) {
				$this->set('cache-control', '');
			}
		}
		public function __toString()
		{
			$cookies = '';
			foreach ($this->getCookies() as $cookie) {
				$cookies.= 'Set-Cookie: ' . $cookie . "\r\n";
			}
			return parent::__toString() . $cookies;
		}
		public function replace(array $headers = array())
		{
			parent::replace($headers);
			if (!isset($this->headers['cache-control'])) {
				$this->set('cache-control', '');
			}
		}
		public function set($key, $values, $replace = true)
		{
			parent::set($key, $values, $replace);
			if (in_array(strtr(strtolower($key), '_', '-'), array('cache-control', 'etag', 'last-modified', 'expires'))) {
				$computed = $this->computeCacheControlValue();
				$this->headers['cache-control'] = array($computed);
				$this->computedCacheControl = $this->parseCacheControl($computed);
			}
		}
		public function remove($key)
		{
			parent::remove($key);
			if ('cache-control' === strtr(strtolower($key), '_', '-')) {
				$this->computedCacheControl = array();
			}
		}
		public function hasCacheControlDirective($key)
		{
			return array_key_exists($key, $this->computedCacheControl);
		}
		public function getCacheControlDirective($key)
		{
			return array_key_exists($key, $this->computedCacheControl) ? $this->computedCacheControl[$key] : null;
		}
		public function setCookie(Cookie $cookie)
		{
			$this->cookies[$cookie->getDomain() ][$cookie->getPath() ][$cookie->getName() ] = $cookie;
		}
		public function removeCookie($name, $path = '/', $domain = null)
		{
			if (null === $path) {
				$path = '/';
			}
			unset($this->cookies[$domain][$path][$name]);
			if (empty($this->cookies[$domain][$path])) {
				unset($this->cookies[$domain][$path]);
				if (empty($this->cookies[$domain])) {
					unset($this->cookies[$domain]);
				}
			}
		}
		public function getCookies($format = self::COOKIES_FLAT)
		{
			if (!in_array($format, array(self::COOKIES_FLAT, self::COOKIES_ARRAY))) {
				throw new \InvalidArgumentException(sprintf('Format "%s" invalid (%s).', $format, implode(', ', array(self::COOKIES_FLAT, self::COOKIES_ARRAY))));
			}
			if (self::COOKIES_ARRAY === $format) {
				return $this->cookies;
			}
			$flattenedCookies = array();
			foreach ($this->cookies as $path) {
				foreach ($path as $cookies) {
					foreach ($cookies as $cookie) {
						$flattenedCookies[] = $cookie;
					}
				}
			}
			return $flattenedCookies;
		}
		public function clearCookie($name, $path = '/', $domain = null)
		{
			$this->setCookie(new Cookie($name, null, 1, $path, $domain));
		}
		public function makeDisposition($disposition, $filename, $filenameFallback = '')
		{
			if (!in_array($disposition, array(self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE))) {
				throw new \InvalidArgumentException(sprintf('The disposition must be either "%s" or "%s".', self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE));
			}
			if ('' == $filenameFallback) {
				$filenameFallback = $filename;
			}
			if (!preg_match('/^[\x20-\x7e]*$/', $filenameFallback)) {
				throw new \InvalidArgumentException('The filename fallback must only contain ASCII characters.');
			}
			if (false !== strpos($filenameFallback, '%')) {
				throw new \InvalidArgumentException('The filename fallback cannot contain the "%" character.');
			}
			if (false !== strpos($filename, '/') || false !== strpos($filename, '\\') || false !== strpos($filenameFallback, '/') || false !== strpos($filenameFallback, '\\')) {
				throw new \InvalidArgumentException('The filename and the fallback cannot contain the "/" and "\\" characters.');
			}
			$output = sprintf('%s; filename="%s"', $disposition, str_replace('"', '\\"', $filenameFallback));
			if ($filename !== $filenameFallback) {
				$output.= sprintf("; filename*=utf-8''%s", rawurlencode($filename));
			}
			return $output;
		}
		protected function computeCacheControlValue()
		{
			if (!$this->cacheControl && !$this->has('ETag') && !$this->has('Last-Modified') && !$this->has('Expires')) {
				return 'no-cache';
			}
			if (!$this->cacheControl) {
				return 'private, must-revalidate';
			}
			$header = $this->getCacheControlHeader();
			if (isset($this->cacheControl['public']) || isset($this->cacheControl['private'])) {
				return $header;
			}
			if (!isset($this->cacheControl['s-maxage'])) {
				return $header . ', private';
			}
			return $header;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\ServerBag.php
	 */
	class ServerBag extends ParameterBag
	{
		public function getHeaders()
		{
			$headers = array();
			foreach ($this->parameters as $key => $value) {
				if (0 === strpos($key, 'HTTP_')) {
					$headers[substr($key, 5) ] = $value;
				} elseif (in_array($key, array('CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE'))) {
					$headers[$key] = $value;
				}
			}
			if (isset($this->parameters['PHP_AUTH_USER'])) {
				$headers['PHP_AUTH_USER'] = $this->parameters['PHP_AUTH_USER'];
				$headers['PHP_AUTH_PW'] = isset($this->parameters['PHP_AUTH_PW']) ? $this->parameters['PHP_AUTH_PW'] : '';
			} else {
				$authorizationHeader = null;
				if (isset($this->parameters['HTTP_AUTHORIZATION'])) {
					$authorizationHeader = $this->parameters['HTTP_AUTHORIZATION'];
				} elseif (isset($this->parameters['REDIRECT_HTTP_AUTHORIZATION'])) {
					$authorizationHeader = $this->parameters['REDIRECT_HTTP_AUTHORIZATION'];
				}
				if ((null !== $authorizationHeader) && (0 === stripos($authorizationHeader, 'basic'))) {
					$exploded = explode(':', base64_decode(substr($authorizationHeader, 6)));
					if (count($exploded) == 2) {
						list($headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']) = $exploded;
					}
				}
			}
			if (isset($headers['PHP_AUTH_USER'])) {
				$headers['AUTHORIZATION'] = 'Basic ' . base64_encode($headers['PHP_AUTH_USER'] . ':' . $headers['PHP_AUTH_PW']);
			}
			return $headers;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\StreamedResponse.php
	 */
	class StreamedResponse extends Response
	{
		protected $callback;
		protected $streamed;
		public function __construct($callback = null, $status = 200, $headers = array())
		{
			parent::__construct(null, $status, $headers);
			if (null !== $callback) {
				$this->setCallback($callback);
			}
			$this->streamed = false;
		}
		public static function create($callback = null, $status = 200, $headers = array())
		{
			return new static ($callback, $status, $headers);
		}
		public function setCallback($callback)
		{
			if (!is_callable($callback)) {
				throw new \LogicException('The Response callback must be a valid PHP callable.');
			}
			$this->callback = $callback;
		}
		public function prepare(Request $request)
		{
			$this->headers->set('Cache-Control', 'no-cache');
			return parent::prepare($request);
		}
		public function sendContent()
		{
			if ($this->streamed) {
				return;
			}
			$this->streamed = true;
			if (null === $this->callback) {
				throw new \LogicException('The Response callback must not be null.');
			}
			call_user_func($this->callback);
		}
		public function setContent($content)
		{
			if (null !== $content) {
				throw new \LogicException('The content cannot be set on a StreamedResponse instance.');
			}
		}
		public function getContent()
		{
			return false;
		}
	}
} namespace Laravel\Session {
	use Laravel\Session\Drivers\Driver;
	use Laravel\Config;
	use Laravel\Session\Drivers\Sweeper;
	use Laravel\Session;
	use Laravel\Cookie;
	use Laravel\Str;
	/**
	 * laravel\session\payload.php
	 */
	class Payload
	{
		public $session;
		public $driver;
		public $exists = true;
		public function __construct(Driver $driver)
		{
			$this->driver = $driver;
		}
		public function load($id)
		{
			if (!is_null($id)) $this->session = $this->driver->load($id);
			if (is_null($this->session) or static ::expired($this->session)) {
				$this->exists = false;
				$this->session = $this->driver->fresh();
			}
			if (!$this->has(Session::csrf_token)) {
				$this->put(Session::csrf_token, Str::random(40));
			}
		}
		protected static function expired($session)
		{
			$lifetime = Config::get('session.lifetime');
			return (time() - $session['last_activity']) > ($lifetime * 60);
		}
		public function has($key)
		{
			return (!is_null($this->get($key)));
		}
		public function get($key, $default = null)
		{
			$session = $this->session['data'];
			if (!is_null($value = array_get($session, $key))) {
				return $value;
			} elseif (!is_null($value = array_get($session[':new:'], $key))) {
				return $value;
			} elseif (!is_null($value = array_get($session[':old:'], $key))) {
				return $value;
			}
			return value($default);
		}
		public function put($key, $value)
		{
			array_set($this->session['data'], $key, $value);
		}
		public function flash($key, $value)
		{
			array_set($this->session['data'][':new:'], $key, $value);
		}
		public function reflash()
		{
			$old = $this->session['data'][':old:'];
			$this->session['data'][':new:'] = array_merge($this->session['data'][':new:'], $old);
		}
		public function keep($keys)
		{
			foreach ((array)$keys as $key) {
				$this->flash($key, $this->get($key));
			}
		}
		public function forget($key)
		{
			array_forget($this->session['data'], $key);
		}
		public function flush()
		{
			$token = $this->token();
			$session = array(Session::csrf_token => $token, ':new:' => array(), ':old:' => array());
			$this->session['data'] = $session;
		}
		public function regenerate()
		{
			$this->session['id'] = $this->driver->id();
			$this->exists = false;
		}
		public function token()
		{
			return $this->get(Session::csrf_token);
		}
		public function activity()
		{
			return $this->session['last_activity'];
		}
		public function save()
		{
			$this->session['last_activity'] = time();
			$this->age();
			$config = Config::get('session');
			$this->driver->save($this->session, $config, $this->exists);
			$this->cookie($config);
			$sweepage = $config['sweepage'];
			if (mt_rand(1, $sweepage[1]) <= $sweepage[0]) {
				$this->sweep();
			}
		}
		public function sweep()
		{
			if ($this->driver instanceof Sweeper) {
				$this->driver->sweep(time() - (Config::get('session.lifetime') * 60));
			}
		}
		protected function age()
		{
			$this->session['data'][':old:'] = $this->session['data'][':new:'];
			$this->session['data'][':new:'] = array();
		}
		protected function cookie($config)
		{
			extract($config, EXTR_SKIP);
			$minutes = (!$expire_on_close) ? $lifetime : 0;
			Cookie::put($cookie, $this->session['id'], $minutes, $path, $domain, $secure);
		}
	}
}
namespace Symfony\Component\HttpFoundation\Session\Flash {
	use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface.php
	 */
	interface FlashBagInterface extends SessionBagInterface {
		public function add($type, $message);
		public function set($type, $message);
		public function peek($type, array $default = array());
		public function peekAll();
		public function get($type, array $default = array());
		public function all();
		public function setAll(array $messages);
		public function has($type);
		public function keys();
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag.php
	 */
	class AutoExpireFlashBag implements FlashBagInterface
	{
		private $name = 'flashes';
		private $flashes = array();
		private $storageKey;
		public function __construct($storageKey = '_sf2_flashes')
		{
			$this->storageKey = $storageKey;
			$this->flashes = array('display' => array(), 'new' => array());
		}
		public function getName()
		{
			return $this->name;
		}
		public function setName($name)
		{
			$this->name = $name;
		}
		public function initialize(array & $flashes)
		{
			$this->flashes = & $flashes;
			$this->flashes['display'] = array_key_exists('new', $this->flashes) ? $this->flashes['new'] : array();
			$this->flashes['new'] = array();
		}
		public function add($type, $message)
		{
			$this->flashes['new'][$type][] = $message;
		}
		public function peek($type, array $default = array())
		{
			return $this->has($type) ? $this->flashes['display'][$type] : $default;
		}
		public function peekAll()
		{
			return array_key_exists('display', $this->flashes) ? (array)$this->flashes['display'] : array();
		}
		public function get($type, array $default = array())
		{
			$return = $default;
			if (!$this->has($type)) {
				return $return;
			}
			if (isset($this->flashes['display'][$type])) {
				$return = $this->flashes['display'][$type];
				unset($this->flashes['display'][$type]);
			}
			return $return;
		}
		public function all()
		{
			$return = $this->flashes['display'];
			$this->flashes = array('new' => array(), 'display' => array());
			return $return;
		}
		public function setAll(array $messages)
		{
			$this->flashes['new'] = $messages;
		}
		public function set($type, $messages)
		{
			$this->flashes['new'][$type] = (array)$messages;
		}
		public function has($type)
		{
			return array_key_exists($type, $this->flashes['display']) && $this->flashes['display'][$type];
		}
		public function keys()
		{
			return array_keys($this->flashes['display']);
		}
		public function getStorageKey()
		{
			return $this->storageKey;
		}
		public function clear()
		{
			return $this->all();
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Flash\FlashBag.php
	 */
	class FlashBag implements FlashBagInterface, \IteratorAggregate, \Countable
	{
		private $name = 'flashes';
		private $flashes = array();
		private $storageKey;
		public function __construct($storageKey = '_sf2_flashes')
		{
			$this->storageKey = $storageKey;
		}
		public function getName()
		{
			return $this->name;
		}
		public function setName($name)
		{
			$this->name = $name;
		}
		public function initialize(array & $flashes)
		{
			$this->flashes = & $flashes;
		}
		public function add($type, $message)
		{
			$this->flashes[$type][] = $message;
		}
		public function peek($type, array $default = array())
		{
			return $this->has($type) ? $this->flashes[$type] : $default;
		}
		public function peekAll()
		{
			return $this->flashes;
		}
		public function get($type, array $default = array())
		{
			if (!$this->has($type)) {
				return $default;
			}
			$return = $this->flashes[$type];
			unset($this->flashes[$type]);
			return $return;
		}
		public function all()
		{
			$return = $this->peekAll();
			$this->flashes = array();
			return $return;
		}
		public function set($type, $messages)
		{
			$this->flashes[$type] = (array)$messages;
		}
		public function setAll(array $messages)
		{
			$this->flashes = $messages;
		}
		public function has($type)
		{
			return array_key_exists($type, $this->flashes) && $this->flashes[$type];
		}
		public function keys()
		{
			return array_keys($this->flashes);
		}
		public function getStorageKey()
		{
			return $this->storageKey;
		}
		public function clear()
		{
			return $this->all();
		}
		public function getIterator()
		{
			return new \ArrayIterator($this->all());
		}
		public function count()
		{
			return count($this->flashes);
		}
	}
}
namespace Laravel\Database\Schema\Grammars
{
	use Laravel\Fluent;
	use Laravel\Database\Schema\Table;
	/**
	 * laravel\database\schema\grammars\grammar.php
	 */
	abstract class Grammar extends \Laravel\Database\Grammar
	{
		public function foreign(Table $table, Fluent $command)
		{
			$name = $command->name;
			$table = $this->wrap($table);
			$on = $this->wrap_table($command->on);
			$foreign = $this->columnize($command->columns);
			$referenced = $this->columnize((array)$command->references);
			$sql = "ALTER TABLE $table ADD CONSTRAINT $name ";
			$sql.= "FOREIGN KEY ($foreign) REFERENCES $on ($referenced)";
			if (!is_null($command->on_delete)) {
				$sql.= " ON DELETE {$command->on_delete}";
			}
			if (!is_null($command->on_update)) {
				$sql.= " ON UPDATE {$command->on_update}";
			}
			return $sql;
		}
		public function drop(Table $table, Fluent $command)
		{
			return 'DROP TABLE ' . $this->wrap($table);
		}
		protected function drop_constraint(Table $table, Fluent $command)
		{
			return "ALTER TABLE " . $this->wrap($table) . " DROP CONSTRAINT " . $command->name;
		}
		public function wrap($value)
		{
			if ($value instanceof Table) {
				return $this->wrap_table($value->name);
			} elseif ($value instanceof Fluent) {
				$value = $value->name;
			}
			return parent::wrap($value);
		}
		protected function type(Fluent $column)
		{
			return $this->{'type_' . $column->type}($column);
		}
		protected function default_value($value)
		{
			if (is_bool($value)) {
				return intval($value);
			}
			return strval($value);
		}
	}
	/**
	 * laravel\database\schema\grammars\mysql.php
	 */
	class MySQL extends Grammar
	{
		public $wrapper = '`%s`';
		public function create(Table $table, Fluent $command)
		{
			$columns = implode(', ', $this->columns($table));
			$sql = 'CREATE TABLE ' . $this->wrap($table) . ' (' . $columns . ')';
			if (!is_null($table->engine)) {
				$sql.= ' ENGINE = ' . $table->engine;
			}
			return $sql;
		}
		public function add(Table $table, Fluent $command)
		{
			$columns = $this->columns($table);
			$columns = implode(', ', array_map(function ($column)
			{
				return 'ADD ' . $column;
			}, $columns));
			return 'ALTER TABLE ' . $this->wrap($table) . ' ' . $columns;
		}
		protected function columns(Table $table)
		{
			$columns = array();
			foreach ($table->columns as $column) {
				$sql = $this->wrap($column) . ' ' . $this->type($column);
				$elements = array('unsigned', 'nullable', 'defaults', 'incrementer');
				foreach ($elements as $element) {
					$sql.= $this->$element($table, $column);
				}
				$columns[] = $sql;
			}
			return $columns;
		}
		protected function unsigned(Table $table, Fluent $column)
		{
			if ($column->type == 'integer' && ($column->unsigned || $column->increment)) {
				return ' UNSIGNED';
			}
		}
		protected function nullable(Table $table, Fluent $column)
		{
			return ($column->nullable) ? ' NULL' : ' NOT NULL';
		}
		protected function defaults(Table $table, Fluent $column)
		{
			if (!is_null($column->default)) {
				return " DEFAULT '" . $this->default_value($column->default) . "'";
			}
		}
		protected function incrementer(Table $table, Fluent $column)
		{
			if ($column->type == 'integer' and $column->increment) {
				return ' AUTO_INCREMENT PRIMARY KEY';
			}
		}
		public function primary(Table $table, Fluent $command)
		{
			return $this->key($table, $command->name(null), 'PRIMARY KEY');
		}
		public function unique(Table $table, Fluent $command)
		{
			return $this->key($table, $command, 'UNIQUE');
		}
		public function fulltext(Table $table, Fluent $command)
		{
			return $this->key($table, $command, 'FULLTEXT');
		}
		public function index(Table $table, Fluent $command)
		{
			return $this->key($table, $command, 'INDEX');
		}
		protected function key(Table $table, Fluent $command, $type)
		{
			$keys = $this->columnize($command->columns);
			$name = $command->name;
			return 'ALTER TABLE ' . $this->wrap($table) . " ADD {$type} {$name}({$keys})";
		}
		public function rename(Table $table, Fluent $command)
		{
			return 'RENAME TABLE ' . $this->wrap($table) . ' TO ' . $this->wrap($command->name);
		}
		public function drop_column(Table $table, Fluent $command)
		{
			$columns = array_map(array($this, 'wrap'), $command->columns);
			$columns = implode(', ', array_map(function ($column)
			{
				return 'DROP ' . $column;
			}, $columns));
			return 'ALTER TABLE ' . $this->wrap($table) . ' ' . $columns;
		}
		public function drop_primary(Table $table, Fluent $command)
		{
			return 'ALTER TABLE ' . $this->wrap($table) . ' DROP PRIMARY KEY';
		}
		public function drop_unique(Table $table, Fluent $command)
		{
			return $this->drop_key($table, $command);
		}
		public function drop_fulltext(Table $table, Fluent $command)
		{
			return $this->drop_key($table, $command);
		}
		public function drop_index(Table $table, Fluent $command)
		{
			return $this->drop_key($table, $command);
		}
		protected function drop_key(Table $table, Fluent $command)
		{
			return 'ALTER TABLE ' . $this->wrap($table) . " DROP INDEX {$command->name}";
		}
		public function drop_foreign(Table $table, Fluent $command)
		{
			return "ALTER TABLE " . $this->wrap($table) . " DROP FOREIGN KEY " . $command->name;
		}
		protected function type_string(Fluent $column)
		{
			return 'VARCHAR(' . $column->length . ')';
		}
		protected function type_integer(Fluent $column)
		{
			return 'INT';
		}
		protected function type_float(Fluent $column)
		{
			return 'FLOAT';
		}
		protected function type_decimal(Fluent $column)
		{
			return "DECIMAL({$column->precision}, {$column->scale})";
		}
		protected function type_boolean(Fluent $column)
		{
			return 'TINYINT(1)';
		}
		protected function type_date(Fluent $column)
		{
			return 'DATETIME';
		}
		protected function type_timestamp(Fluent $column)
		{
			return 'TIMESTAMP';
		}
		protected function type_text(Fluent $column)
		{
			return 'TEXT';
		}
		protected function type_blob(Fluent $column)
		{
			return 'BLOB';
		}
	}
	/**
	 * laravel\database\schema\grammars\postgres.php
	 */
	class Postgres extends Grammar
	{
		public function create(Table $table, Fluent $command)
		{
			$columns = implode(', ', $this->columns($table));
			$sql = 'CREATE TABLE ' . $this->wrap($table) . ' (' . $columns . ')';
			return $sql;
		}
		public function add(Table $table, Fluent $command)
		{
			$columns = $this->columns($table);
			$columns = implode(', ', array_map(function ($column)
			{
				return 'ADD COLUMN ' . $column;
			}, $columns));
			return 'ALTER TABLE ' . $this->wrap($table) . ' ' . $columns;
		}
		protected function columns(Table $table)
		{
			$columns = array();
			foreach ($table->columns as $column) {
				$sql = $this->wrap($column) . ' ' . $this->type($column);
				$elements = array('incrementer', 'nullable', 'defaults');
				foreach ($elements as $element) {
					$sql.= $this->$element($table, $column);
				}
				$columns[] = $sql;
			}
			return $columns;
		}
		protected function nullable(Table $table, Fluent $column)
		{
			return ($column->nullable) ? ' NULL' : ' NOT NULL';
		}
		protected function defaults(Table $table, Fluent $column)
		{
			if (!is_null($column->default)) {
				return " DEFAULT '" . $this->default_value($column->default) . "'";
			}
		}
		protected function incrementer(Table $table, Fluent $column)
		{
			if ($column->type == 'integer' and $column->increment) {
				return ' PRIMARY KEY';
			}
		}
		public function primary(Table $table, Fluent $command)
		{
			$columns = $this->columnize($command->columns);
			return 'ALTER TABLE ' . $this->wrap($table) . " ADD PRIMARY KEY ({$columns})";
		}
		public function unique(Table $table, Fluent $command)
		{
			$table = $this->wrap($table);
			$columns = $this->columnize($command->columns);
			return "ALTER TABLE $table ADD CONSTRAINT " . $command->name . " UNIQUE ($columns)";
		}
		public function fulltext(Table $table, Fluent $command)
		{
			$name = $command->name;
			$columns = $this->columnize($command->columns);
			return "CREATE INDEX {$name} ON " . $this->wrap($table) . " USING gin({$columns})";
		}
		public function index(Table $table, Fluent $command)
		{
			return $this->key($table, $command);
		}
		protected function key(Table $table, Fluent $command, $unique = false)
		{
			$columns = $this->columnize($command->columns);
			$create = ($unique) ? 'CREATE UNIQUE' : 'CREATE';
			return $create . " INDEX {$command->name} ON " . $this->wrap($table) . " ({$columns})";
		}
		public function rename(Table $table, Fluent $command)
		{
			return 'ALTER TABLE ' . $this->wrap($table) . ' RENAME TO ' . $this->wrap($command->name);
		}
		public function drop_column(Table $table, Fluent $command)
		{
			$columns = array_map(array($this, 'wrap'), $command->columns);
			$columns = implode(', ', array_map(function ($column)
			{
				return 'DROP COLUMN ' . $column;
			}, $columns));
			return 'ALTER TABLE ' . $this->wrap($table) . ' ' . $columns;
		}
		public function drop_primary(Table $table, Fluent $command)
		{
			return 'ALTER TABLE ' . $this->wrap($table) . ' DROP CONSTRAINT ' . $table->name . '_pkey';
		}
		public function drop_unique(Table $table, Fluent $command)
		{
			return $this->drop_constraint($table, $command);
		}
		public function drop_fulltext(Table $table, Fluent $command)
		{
			return $this->drop_key($table, $command);
		}
		public function drop_index(Table $table, Fluent $command)
		{
			return $this->drop_key($table, $command);
		}
		protected function drop_key(Table $table, Fluent $command)
		{
			return 'DROP INDEX ' . $command->name;
		}
		public function drop_foreign(Table $table, Fluent $command)
		{
			return $this->drop_constraint($table, $command);
		}
		protected function type_string(Fluent $column)
		{
			return 'VARCHAR(' . $column->length . ')';
		}
		protected function type_integer(Fluent $column)
		{
			return ($column->increment) ? 'SERIAL' : 'BIGINT';
		}
		protected function type_float(Fluent $column)
		{
			return 'REAL';
		}
		protected function type_decimal(Fluent $column)
		{
			return "DECIMAL({$column->precision}, {$column->scale})";
		}
		protected function type_boolean(Fluent $column)
		{
			return 'SMALLINT';
		}
		protected function type_date(Fluent $column)
		{
			return 'TIMESTAMP(0) WITHOUT TIME ZONE';
		}
		protected function type_timestamp(Fluent $column)
		{
			return 'TIMESTAMP';
		}
		protected function type_text(Fluent $column)
		{
			return 'TEXT';
		}
		protected function type_blob(Fluent $column)
		{
			return 'BYTEA';
		}
	}
	/**
	 * laravel\database\schema\grammars\sqlite.php
	 */
	class SQLite extends Grammar
	{
		public function create(Table $table, Fluent $command)
		{
			$columns = implode(', ', $this->columns($table));
			$sql = 'CREATE TABLE ' . $this->wrap($table) . ' (' . $columns;
			$primary = array_first($table->commands, function ($key, $value)
			{
				return $value->type == 'primary';
			});
			if (!is_null($primary)) {
				$columns = $this->columnize($primary->columns);
				$sql.= ", PRIMARY KEY ({$columns})";
			}
			return $sql.= ')';
		}
		public function add(Table $table, Fluent $command)
		{
			$columns = $this->columns($table);
			$columns = array_map(function ($column)
			{
				return 'ADD COLUMN ' . $column;
			}, $columns);
			foreach ($columns as $column) {
				$sql[] = 'ALTER TABLE ' . $this->wrap($table) . ' ' . $column;
			}
			return (array)$sql;
		}
		protected function columns(Table $table)
		{
			$columns = array();
			foreach ($table->columns as $column) {
				$sql = $this->wrap($column) . ' ' . $this->type($column);
				$elements = array('nullable', 'defaults', 'incrementer');
				foreach ($elements as $element) {
					$sql.= $this->$element($table, $column);
				}
				$columns[] = $sql;
			}
			return $columns;
		}
		protected function nullable(Table $table, Fluent $column)
		{
			return ' NULL';
		}
		protected function defaults(Table $table, Fluent $column)
		{
			if (!is_null($column->default)) {
				return ' DEFAULT ' . $this->wrap($this->default_value($column->default));
			}
		}
		protected function incrementer(Table $table, Fluent $column)
		{
			if ($column->type == 'integer' and $column->increment) {
				return ' PRIMARY KEY AUTOINCREMENT';
			}
		}
		public function unique(Table $table, Fluent $command)
		{
			return $this->key($table, $command, true);
		}
		public function fulltext(Table $table, Fluent $command)
		{
			$columns = $this->columnize($command->columns);
			return 'CREATE VIRTUAL TABLE ' . $this->wrap($table) . " USING fts4({$columns})";
		}
		public function index(Table $table, Fluent $command)
		{
			return $this->key($table, $command);
		}
		protected function key(Table $table, Fluent $command, $unique = false)
		{
			$columns = $this->columnize($command->columns);
			$create = ($unique) ? 'CREATE UNIQUE' : 'CREATE';
			return $create . " INDEX {$command->name} ON " . $this->wrap($table) . " ({$columns})";
		}
		public function rename(Table $table, Fluent $command)
		{
			return 'ALTER TABLE ' . $this->wrap($table) . ' RENAME TO ' . $this->wrap($command->name);
		}
		public function drop_unique(Table $table, Fluent $command)
		{
			return $this->drop_key($table, $command);
		}
		public function drop_index(Table $table, Fluent $command)
		{
			return $this->drop_key($table, $command);
		}
		protected function drop_key(Table $table, Fluent $command)
		{
			return 'DROP INDEX ' . $this->wrap($command->name);
		}
		protected function type_string(Fluent $column)
		{
			return 'VARCHAR';
		}
		protected function type_integer(Fluent $column)
		{
			return 'INTEGER';
		}
		protected function type_float(Fluent $column)
		{
			return 'FLOAT';
		}
		protected function type_decimal(Fluent $column)
		{
			return 'FLOAT';
		}
		protected function type_boolean(Fluent $column)
		{
			return 'INTEGER';
		}
		protected function type_date(Fluent $column)
		{
			return 'DATETIME';
		}
		protected function type_timestamp(Fluent $column)
		{
			return 'DATETIME';
		}
		protected function type_text(Fluent $column)
		{
			return 'TEXT';
		}
		protected function type_blob(Fluent $column)
		{
			return 'BLOB';
		}
	}
	/**
	 * laravel\database\schema\grammars\sqlserver.php
	 */
	class SQLServer extends Grammar
	{
		public $wrapper = '[%s]';
		public function create(Table $table, Fluent $command)
		{
			$columns = implode(', ', $this->columns($table));
			$sql = 'CREATE TABLE ' . $this->wrap($table) . ' (' . $columns . ')';
			return $sql;
		}
		public function add(Table $table, Fluent $command)
		{
			$columns = $this->columns($table);
			$columns = implode(', ', array_map(function ($column)
			{
				return 'ADD ' . $column;
			}, $columns));
			return 'ALTER TABLE ' . $this->wrap($table) . ' ' . $columns;
		}
		protected function columns(Table $table)
		{
			$columns = array();
			foreach ($table->columns as $column) {
				$sql = $this->wrap($column) . ' ' . $this->type($column);
				$elements = array('incrementer', 'nullable', 'defaults');
				foreach ($elements as $element) {
					$sql.= $this->$element($table, $column);
				}
				$columns[] = $sql;
			}
			return $columns;
		}
		protected function nullable(Table $table, Fluent $column)
		{
			return ($column->nullable) ? ' NULL' : ' NOT NULL';
		}
		protected function defaults(Table $table, Fluent $column)
		{
			if (!is_null($column->default)) {
				return " DEFAULT '" . $this->default_value($column->default) . "'";
			}
		}
		protected function incrementer(Table $table, Fluent $column)
		{
			if ($column->type == 'integer' and $column->increment) {
				return ' IDENTITY PRIMARY KEY';
			}
		}
		public function primary(Table $table, Fluent $command)
		{
			$name = $command->name;
			$columns = $this->columnize($command->columns);
			return 'ALTER TABLE ' . $this->wrap($table) . " ADD CONSTRAINT {$name} PRIMARY KEY ({$columns})";
		}
		public function unique(Table $table, Fluent $command)
		{
			return $this->key($table, $command, true);
		}
		public function fulltext(Table $table, Fluent $command)
		{
			$columns = $this->columnize($command->columns);
			$table = $this->wrap($table);
			$sql[] = "CREATE FULLTEXT CATALOG {$command->catalog}";
			$create = "CREATE FULLTEXT INDEX ON " . $table . " ({$columns}) ";
			$sql[] = $create.= "KEY INDEX {$command->key} ON {$command->catalog}";
			return $sql;
		}
		public function index(Table $table, Fluent $command)
		{
			return $this->key($table, $command);
		}
		protected function key(Table $table, Fluent $command, $unique = false)
		{
			$columns = $this->columnize($command->columns);
			$create = ($unique) ? 'CREATE UNIQUE' : 'CREATE';
			return $create . " INDEX {$command->name} ON " . $this->wrap($table) . " ({$columns})";
		}
		public function rename(Table $table, Fluent $command)
		{
			return 'ALTER TABLE ' . $this->wrap($table) . ' RENAME TO ' . $this->wrap($command->name);
		}
		public function drop_column(Table $table, Fluent $command)
		{
			$columns = array_map(array($this, 'wrap'), $command->columns);
			$columns = implode(', ', array_map(function ($column)
			{
				return 'DROP ' . $column;
			}, $columns));
			return 'ALTER TABLE ' . $this->wrap($table) . ' ' . $columns;
		}
		public function drop_primary(Table $table, Fluent $command)
		{
			return 'ALTER TABLE ' . $this->wrap($table) . ' DROP CONSTRAINT ' . $command->name;
		}
		public function drop_unique(Table $table, Fluent $command)
		{
			return $this->drop_key($table, $command);
		}
		public function drop_fulltext(Table $table, Fluent $command)
		{
			$sql[] = "DROP FULLTEXT INDEX " . $command->name;
			$sql[] = "DROP FULLTEXT CATALOG " . $command->catalog;
			return $sql;
		}
		public function drop_index(Table $table, Fluent $command)
		{
			return $this->drop_key($table, $command);
		}
		protected function drop_key(Table $table, Fluent $command)
		{
			return "DROP INDEX {$command->name} ON " . $this->wrap($table);
		}
		public function drop_foreign(Table $table, Fluent $command)
		{
			return $this->drop_constraint($table, $command);
		}
		protected function type_string(Fluent $column)
		{
			return 'NVARCHAR(' . $column->length . ')';
		}
		protected function type_integer(Fluent $column)
		{
			return 'INT';
		}
		protected function type_float(Fluent $column)
		{
			return 'FLOAT';
		}
		protected function type_decimal(Fluent $column)
		{
			return "DECIMAL({$column->precision}, {$column->scale})";
		}
		protected function type_boolean(Fluent $column)
		{
			return 'TINYINT';
		}
		protected function type_date(Fluent $column)
		{
			return 'DATETIME';
		}
		protected function type_timestamp(Fluent $column)
		{
			return 'TIMESTAMP';
		}
		protected function type_text(Fluent $column)
		{
			return 'NVARCHAR(MAX)';
		}
		protected function type_blob(Fluent $column)
		{
			return 'VARBINARY(MAX)';
		}
	}
}
namespace Laravel\Database\Eloquent\Relationships
{
	use Laravel\Database\Eloquent\Query;
	use Laravel\Str;
	use Laravel\Database\Eloquent\Pivot;
	use Laravel\Database\Eloquent\Model;
	/**
	 * laravel\database\eloquent\relationships\relationship.php
	 */
	abstract class Relationship extends Query
	{
		protected $base;
		public function __construct($model, $associated, $foreign)
		{
			$this->foreign = $foreign;
			if ($associated instanceof Model) {
				$this->model = $associated;
			} else {
				$this->model = new $associated;
			}
			if ($model instanceof Model) {
				$this->base = $model;
			} else {
				$this->base = new $model;
			}
			$this->table = $this->table();
			$this->constrain();
		}
		public static function foreign($model, $foreign = null)
		{
			if (!is_null($foreign)) return $foreign;
			if (is_object($model)) {
				$model = class_basename($model);
			}
			return strtolower(basename($model) . '_id');
		}
		protected function fresh_model($attributes = array())
		{
			$class = get_class($this->model);
			return new $class($attributes);
		}
		public function foreign_key()
		{
			return static ::foreign($this->base, $this->foreign);
		}
		public function keys($results)
		{
			$keys = array();
			foreach ($results as $result) {
				$keys[] = $result->get_key();
			}
			return array_unique($keys);
		}
		public function with($includes)
		{
			$this->model->includes = (array)$includes;
			return $this;
		}
	}
	/**
	 * laravel\database\eloquent\relationships\has_one_or_many.php
	 */
	class Has_One_Or_Many extends Relationship
	{
		public function insert($attributes)
		{
			if ($attributes instanceof Model) {
				$attributes->set_attribute($this->foreign_key(), $this->base->get_key());
				return $attributes->save() ? $attributes : false;
			} else {
				$attributes[$this->foreign_key() ] = $this->base->get_key();
				return $this->model->create($attributes);
			}
		}
		public function update(array $attributes)
		{
			if ($this->model->timestamps()) {
				$attributes['updated_at'] = new \DateTime;
			}
			return $this->table->update($attributes);
		}
		protected function constrain()
		{
			$this->table->where($this->foreign_key(), '=', $this->base->get_key());
		}
		public function eagerly_constrain($results)
		{
			$this->table->where_in($this->foreign_key(), $this->keys($results));
		}
	}
	/**
	 * laravel\database\eloquent\relationships\belongs_to.php
	 */
	class Belongs_To extends Relationship
	{
		public function results()
		{
			return parent::first();
		}
		public function update($attributes)
		{
			$attributes = ($attributes instanceof Model) ? $attributes->get_dirty() : $attributes;
			return $this->model->update($this->foreign_value(), $attributes);
		}
		protected function constrain()
		{
			$this->table->where($this->model->key(), '=', $this->foreign_value());
		}
		public function initialize(&$parents, $relationship)
		{
			foreach ($parents as & $parent) {
				$parent->relationships[$relationship] = null;
			}
		}
		public function eagerly_constrain($results)
		{
			$keys = array();
			foreach ($results as $result) {
				if (!is_null($key = $result->{$this->foreign_key() })) {
					$keys[] = $key;
				}
			}
			if (count($keys) == 0) $keys = array(0);
			$this->table->where_in($this->model->key(), array_unique($keys));
		}
		public function match($relationship, &$children, $parents)
		{
			$foreign = $this->foreign_key();
			$dictionary = array();
			foreach ($parents as $parent) {
				$dictionary[$parent->get_key() ] = $parent;
			}
			foreach ($children as $child) {
				if (array_key_exists($child->$foreign, $dictionary)) {
					$child->relationships[$relationship] = $dictionary[$child->$foreign];
				}
			}
		}
		public function foreign_value()
		{
			return $this->base->get_attribute($this->foreign);
		}
		public function bind($id)
		{
			$this->base->fill(array($this->foreign => $id))->save();
			return $this->base;
		}
	}
	/**
	 * laravel\database\eloquent\relationships\has_many.php
	 */
	class Has_Many extends Has_One_Or_Many
	{
		public function results()
		{
			return parent::get();
		}
		public function save($models)
		{
			if (!is_array($models)) $models = array($models);
			$current = $this->table->lists($this->model->key());
			foreach ($models as $attributes) {
				$class = get_class($this->model);
				if ($attributes instanceof $class) {
					$model = $attributes;
				} else {
					$model = $this->fresh_model($attributes);
				}
				$foreign = $this->foreign_key();
				$model->$foreign = $this->base->get_key();
				$id = $model->get_key();
				$model->exists = (!is_null($id) and in_array($id, $current));
				$model->original = array();
				$model->save();
			}
			return true;
		}
		public function initialize(&$parents, $relationship)
		{
			foreach ($parents as & $parent) {
				$parent->relationships[$relationship] = array();
			}
		}
		public function match($relationship, &$parents, $children)
		{
			$foreign = $this->foreign_key();
			$dictionary = array();
			foreach ($children as $child) {
				$dictionary[$child->$foreign][] = $child;
			}
			foreach ($parents as $parent) {
				if (array_key_exists($key = $parent->get_key(), $dictionary)) {
					$parent->relationships[$relationship] = $dictionary[$key];
				}
			}
		}
	}
	/**
	 * laravel\database\eloquent\relationships\has_many_and_belongs_to.php
	 */
	class Has_Many_And_Belongs_To extends Relationship
	{
		protected $joining;
		protected $other;
		protected $with = array('id');
		public function __construct($model, $associated, $table, $foreign, $other)
		{
			$this->other = $other;
			$this->joining = $table ? : $this->joining($model, $associated);
			if (Pivot::$timestamps) {
				$this->with[] = 'created_at';
				$this->with[] = 'updated_at';
			}
			parent::__construct($model, $associated, $foreign);
		}
		protected function joining($model, $associated)
		{
			$models = array(class_basename($model), class_basename($associated));
			sort($models);
			return strtolower($models[0] . '_' . $models[1]);
		}
		public function results()
		{
			return parent::get();
		}
		public function attach($id, $attributes = array())
		{
			if ($id instanceof Model) $id = $id->get_key();
			$joining = array_merge($this->join_record($id), $attributes);
			return $this->insert_joining($joining);
		}
		public function detach($ids)
		{
			if ($ids instanceof Model) $ids = array($ids->get_key());
			elseif (!is_array($ids)) $ids = array($ids);
			return $this->pivot()->where_in($this->other_key(), $ids)->delete();
		}
		public function sync($ids)
		{
			$current = $this->pivot()->lists($this->other_key());
			$ids = (array)$ids;
			foreach ($ids as $id) {
				if (!in_array($id, $current)) {
					$this->attach($id);
				}
			}
			$detach = array_diff($current, $ids);
			if (count($detach) > 0) {
				$this->detach($detach);
			}
		}
		public function insert($attributes, $joining = array())
		{
			if ($attributes instanceof Model) {
				$attributes = $attributes->attributes;
			}
			$model = $this->model->create($attributes);
			if ($model instanceof Model) {
				$joining = array_merge($this->join_record($model->get_key()), $joining);
				$result = $this->insert_joining($joining);
			}
			return $model instanceof Model and $result;
		}
		public function delete()
		{
			return $this->pivot()->delete();
		}
		protected function join_record($id)
		{
			return array($this->foreign_key() => $this->base->get_key(), $this->other_key() => $id);
		}
		protected function insert_joining($attributes)
		{
			if (Pivot::$timestamps) {
				$attributes['created_at'] = new \DateTime;
				$attributes['updated_at'] = $attributes['created_at'];
			}
			return $this->joining_table()->insert($attributes);
		}
		protected function joining_table()
		{
			return $this->connection()->table($this->joining);
		}
		protected function constrain()
		{
			$other = $this->other_key();
			$foreign = $this->foreign_key();
			$this->set_select($foreign, $other)->set_join($other)->set_where($foreign);
		}
		protected function set_select($foreign, $other)
		{
			$columns = array($this->model->table() . '.*');
			$this->with = array_merge($this->with, array($foreign, $other));
			foreach ($this->with as $column) {
				$columns[] = $this->joining . '.' . $column . ' as pivot_' . $column;
			}
			$this->table->select($columns);
			return $this;
		}
		protected function set_join($other)
		{
			$this->table->join($this->joining, $this->associated_key(), '=', $this->joining . '.' . $other);
			return $this;
		}
		protected function set_where($foreign)
		{
			$this->table->where($this->joining . '.' . $foreign, '=', $this->base->get_key());
			return $this;
		}
		public function initialize(&$parents, $relationship)
		{
			foreach ($parents as & $parent) {
				$parent->relationships[$relationship] = array();
			}
		}
		public function eagerly_constrain($results)
		{
			$this->table->where_in($this->joining . '.' . $this->foreign_key(), $this->keys($results));
		}
		public function match($relationship, &$parents, $children)
		{
			$foreign = $this->foreign_key();
			$dictionary = array();
			foreach ($children as $child) {
				$dictionary[$child->pivot->$foreign][] = $child;
			}
			foreach ($parents as $parent) {
				if (array_key_exists($key = $parent->get_key(), $dictionary)) {
					$parent->relationships[$relationship] = $dictionary[$key];
				}
			}
		}
		protected function hydrate_pivot(&$results)
		{
			foreach ($results as & $result) {
				$pivot = new Pivot($this->joining, $this->model->connection());
				foreach ($result->attributes as $key => $value) {
					if (starts_with($key, 'pivot_')) {
						$pivot->{substr($key, 6) } = $value;
						$result->purge($key);
					}
				}
				$result->relationships['pivot'] = $pivot;
				$pivot->sync() and $result->sync();
			}
		}
		public function with($columns)
		{
			$columns = (is_array($columns)) ? $columns : func_get_args();
			$this->with = array_unique(array_merge($this->with, $columns));
			$this->set_select($this->foreign_key(), $this->other_key());
			return $this;
		}
		public function pivot()
		{
			$pivot = new Pivot($this->joining, $this->model->connection());
			return new Has_Many($this->base, $pivot, $this->foreign_key());
		}
		protected function other_key()
		{
			return Relationship::foreign($this->model, $this->other);
		}
		protected function associated_key()
		{
			return $this->model->table() . '.' . $this->model->key();
		}
	}
	/**
	 * laravel\database\eloquent\relationships\has_one.php
	 */
	class Has_One extends Has_One_Or_Many
	{
		public function results()
		{
			return parent::first();
		}
		public function initialize(&$parents, $relationship)
		{
			foreach ($parents as & $parent) {
				$parent->relationships[$relationship] = null;
			}
		}
		public function match($relationship, &$parents, $children)
		{
			$foreign = $this->foreign_key();
			$dictionary = array();
			foreach ($children as $child) {
				$dictionary[$child->$foreign] = $child;
			}
			foreach ($parents as $parent) {
				if (array_key_exists($key = $parent->get_key(), $dictionary)) {
					$parent->relationships[$relationship] = $dictionary[$key];
				}
			}
		}
	}
}
namespace Laravel\Database\Query
{
	/**
	 * laravel\database\query\join.php
	 */
	class Join
	{
		public $type;
		public $table;
		public $clauses = array();
		public function __construct($type, $table)
		{
			$this->type = $type;
			$this->table = $table;
		}
		public function on($column1, $operator, $column2, $connector = 'AND')
		{
			$this->clauses[] = compact('column1', 'operator', 'column2', 'connector');
			return $this;
		}
		public function or_on($column1, $operator, $column2)
		{
			return $this->on($column1, $operator, $column2, 'OR');
		}
	}
}
namespace Laravel\Database\Schema
{
	use Laravel\Fluent;
	/**
	 * laravel\database\schema\table.php
	 */
	class Table
	{
		public $name;
		public $connection;
		public $engine;
		public $columns = array();
		public $commands = array();
		public function __construct($name)
		{
			$this->name = $name;
		}
		public function create()
		{
			return $this->command(__FUNCTION__);
		}
		public function primary($columns, $name = null)
		{
			return $this->key(__FUNCTION__, $columns, $name);
		}
		public function unique($columns, $name = null)
		{
			return $this->key(__FUNCTION__, $columns, $name);
		}
		public function fulltext($columns, $name = null)
		{
			return $this->key(__FUNCTION__, $columns, $name);
		}
		public function index($columns, $name = null)
		{
			return $this->key(__FUNCTION__, $columns, $name);
		}
		public function foreign($columns, $name = null)
		{
			return $this->key(__FUNCTION__, $columns, $name);
		}
		public function key($type, $columns, $name)
		{
			$columns = (array)$columns;
			if (is_null($name)) {
				$name = str_replace(array('-', '.'), '_', $this->name);
				$name = $name . '_' . implode('_', $columns) . '_' . $type;
			}
			return $this->command($type, compact('name', 'columns'));
		}
		public function rename($name)
		{
			return $this->command(__FUNCTION__, compact('name'));
		}
		public function drop()
		{
			return $this->command(__FUNCTION__);
		}
		public function drop_column($columns)
		{
			return $this->command(__FUNCTION__, array('columns' => (array)$columns));
		}
		public function drop_primary($name = null)
		{
			return $this->drop_key(__FUNCTION__, $name);
		}
		public function drop_unique($name)
		{
			return $this->drop_key(__FUNCTION__, $name);
		}
		public function drop_fulltext($name)
		{
			return $this->drop_key(__FUNCTION__, $name);
		}
		public function drop_index($name)
		{
			return $this->drop_key(__FUNCTION__, $name);
		}
		public function drop_foreign($name)
		{
			return $this->drop_key(__FUNCTION__, $name);
		}
		protected function drop_key($type, $name)
		{
			return $this->command($type, compact('name'));
		}
		public function increments($name)
		{
			return $this->integer($name, true);
		}
		public function string($name, $length = 200)
		{
			return $this->column(__FUNCTION__, compact('name', 'length'));
		}
		public function integer($name, $increment = false)
		{
			return $this->column(__FUNCTION__, compact('name', 'increment'));
		}
		public function float($name)
		{
			return $this->column(__FUNCTION__, compact('name'));
		}
		public function decimal($name, $precision, $scale)
		{
			return $this->column(__FUNCTION__, compact('name', 'precision', 'scale'));
		}
		public function boolean($name)
		{
			return $this->column(__FUNCTION__, compact('name'));
		}
		public function timestamps()
		{
			$this->date('created_at');
			$this->date('updated_at');
		}
		public function date($name)
		{
			return $this->column(__FUNCTION__, compact('name'));
		}
		public function timestamp($name)
		{
			return $this->column(__FUNCTION__, compact('name'));
		}
		public function text($name)
		{
			return $this->column(__FUNCTION__, compact('name'));
		}
		public function blob($name)
		{
			return $this->column(__FUNCTION__, compact('name'));
		}
		public function on($connection)
		{
			$this->connection = $connection;
		}
		public function creating()
		{
			return !is_null(array_first($this->commands, function ($key, $value)
			{
				return $value->type == 'create';
			}));
		}
		protected function command($type, $parameters = array())
		{
			$parameters = array_merge(compact('type'), $parameters);
			return $this->commands[] = new Fluent($parameters);
		}
		protected function column($type, $parameters = array())
		{
			$parameters = array_merge(compact('type'), $parameters);
			return $this->columns[] = new Fluent($parameters);
		}
	}
}
namespace Symfony\Component\HttpFoundation\File\Exception
{
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException.php
	 */
	class AccessDeniedException extends FileException
	{
		public function __construct($path)
		{
			parent::__construct(sprintf('The file %s could not be accessed', $path));
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\Exception\FileException.php
	 */
	class FileException extends \RuntimeException
	{
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException.php
	 */
	class FileNotFoundException extends FileException
	{
		public function __construct($path)
		{
			parent::__construct(sprintf('The file "%s" does not exist', $path));
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException.php
	 */
	class UnexpectedTypeException extends FileException
	{
		public function __construct($value, $expectedType)
		{
			parent::__construct(sprintf('Expected argument of type %s, %s given', $expectedType, is_object($value) ? get_class($value) : gettype($value)));
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\Exception\UploadException.php
	 */
	class UploadException extends FileException
	{
	}
}
namespace Laravel
{
	use Closure;
	use Laravel\Routing\Router;
	use Symfony\Component\HttpFoundation\LaravelResponse as FoundationResponse;
	use Laravel\Database\Expression;
	use Symfony\Component\HttpFoundation\LaravelRequest as RequestFoundation;
	use Laravel\Database\Connection;
	use Symfony\Component\HttpFoundation\ResponseHeaderBag;
	use FilesystemIterator as fIterator;
	use Laravel\Routing\Route;
	#use Router;
	use ArrayAccess;
	/**
	 * laravel\asset.php
	 */
	class Asset
	{
		public static $containers = array();
		public static function container($container = 'default')
		{
			if (!isset(static ::$containers[$container])) {
				static ::$containers[$container] = new Asset_Container($container);
			}
			return static ::$containers[$container];
		}
		public static function __callStatic($method, $parameters)
		{
			return call_user_func_array(array(static ::container(), $method), $parameters);
		}
	}
	class Asset_Container
	{
		public $name;
		public $bundle = DEFAULT_BUNDLE;
		public $assets = array();
		public function __construct($name)
		{
			$this->name = $name;
		}
		public function add($name, $source, $dependencies = array(), $attributes = array())
		{
			$type = (pathinfo($source, PATHINFO_EXTENSION) == 'css') ? 'style' : 'script';
			return $this->$type($name, $source, $dependencies, $attributes);
		}
		public function style($name, $source, $dependencies = array(), $attributes = array())
		{
			if (!array_key_exists('media', $attributes)) {
				$attributes['media'] = 'all';
			}
			$this->register('style', $name, $source, $dependencies, $attributes);
			return $this;
		}
		public function script($name, $source, $dependencies = array(), $attributes = array())
		{
			$this->register('script', $name, $source, $dependencies, $attributes);
			return $this;
		}
		public function path($source)
		{
			return Bundle::assets($this->bundle) . $source;
		}
		public function bundle($bundle)
		{
			$this->bundle = $bundle;
			return $this;
		}
		protected function register($type, $name, $source, $dependencies, $attributes)
		{
			$dependencies = (array)$dependencies;
			$attributes = (array)$attributes;
			$this->assets[$type][$name] = compact('source', 'dependencies', 'attributes');
		}
		public function styles()
		{
			return $this->group('style');
		}
		public function scripts()
		{
			return $this->group('script');
		}
		protected function group($group)
		{
			if (!isset($this->assets[$group]) or count($this->assets[$group]) == 0) return '';
			$assets = '';
			foreach ($this->arrange($this->assets[$group]) as $name => $data) {
				$assets.= $this->asset($group, $name);
			}
			return $assets;
		}
		protected function asset($group, $name)
		{
			if (!isset($this->assets[$group][$name])) return '';
			$asset = $this->assets[$group][$name];
			if (filter_var($asset['source'], FILTER_VALIDATE_URL) === false) {
				$asset['source'] = $this->path($asset['source']);
			}
			return HTML::$group($asset['source'], $asset['attributes']);
		}
		protected function arrange($assets)
		{
			list($original, $sorted) = array($assets, array());
			while (count($assets) > 0) {
				foreach ($assets as $asset => $value) {
					$this->evaluate_asset($asset, $value, $original, $sorted, $assets);
				}
			}
			return $sorted;
		}
		protected function evaluate_asset($asset, $value, $original, &$sorted, &$assets)
		{
			if (count($assets[$asset]['dependencies']) == 0) {
				$sorted[$asset] = $value;
				unset($assets[$asset]);
			} else {
				foreach ($assets[$asset]['dependencies'] as $key => $dependency) {
					if (!$this->dependency_is_valid($asset, $dependency, $original, $assets)) {
						unset($assets[$asset]['dependencies'][$key]);
						continue;
					}
					if (!isset($sorted[$dependency])) continue;
					unset($assets[$asset]['dependencies'][$key]);
				}
			}
		}
		protected function dependency_is_valid($asset, $dependency, $original, $assets)
		{
			if (!isset($original[$dependency])) {
				return false;
			} elseif ($dependency === $asset) {
				throw new \Exception("Asset [$asset] is dependent on itself.");
			} elseif (isset($assets[$dependency]) and in_array($asset, $assets[$dependency]['dependencies'])) {
				throw new \Exception("Assets [$asset] and [$dependency] have a circular dependency.");
			}
			return true;
		}
	}
	/**
	 * laravel\view.php
	 */
	class View implements ArrayAccess
	{
		public $view;
		public $data;
		public $path;
		public static $shared = array();
		public static $names = array();
		public static $cache = array();
		public static $last;
		public static $render_count = 0;
		const loader = 'laravel.view.loader';
		const engine = 'laravel.view.engine';
		public function __construct($view, $data = array())
		{
			$this->view = $view;
			$this->data = $data;
			if (starts_with($view, 'path: ')) {
				$this->path = substr($view, 6);
			} else {
				$this->path = $this->path($view);
			}
			if (!isset($this->data['errors'])) {
				if (Session::started() and Session::has('errors')) {
					$this->data['errors'] = Session::get('errors');
				} else {
					$this->data['errors'] = new Messages;
				}
			}
		}
		public static function exists($view, $return_path = false)
		{
			if (starts_with($view, 'name: ') and array_key_exists($name = substr($view, 6), static ::$names)) {
				$view = static ::$names[$name];
			}
			list($bundle, $view) = Bundle::parse($view);
			$view = str_replace('.', '/', $view);
			$path = Event::until(static ::loader, array($bundle, $view));
			if (!is_null($path)) {
				return $return_path ? $path : true;
			}
			return false;
		}
		protected function path($view)
		{
			if ($path = $this->exists($view, true)) {
				return $path;
			}
			throw new \Exception("View [$view] doesn't exist.");
		}
		public static function file($bundle, $view, $directory)
		{
			$directory = str_finish($directory, DS);
			if (file_exists($path = $directory . $view . EXT)) {
				return $path;
			} elseif (file_exists($path = $directory . $view . BLADE_EXT)) {
				return $path;
			}
		}
		public static function make($view, $data = array())
		{
			return new static ($view, $data);
		}
		public static function of($name, $data = array())
		{
			return new static (static ::$names[$name], $data);
		}
		public static function name($view, $name)
		{
			static ::$names[$name] = $view;
		}
		public static function composer($views, $composer)
		{
			$views = (array)$views;
			foreach ($views as $view) {
				Event::listen("laravel.composing: {$view}", $composer);
			}
		}
		public static function render_each($view, array $data, $iterator, $empty = 'raw|')
		{
			$result = '';
			if (count($data) > 0) {
				foreach ($data as $key => $value) {
					$with = array('key' => $key, $iterator => $value);
					$result.= render($view, $with);
				}
			} else {
				if (starts_with($empty, 'raw|')) {
					$result = substr($empty, 4);
				} else {
					$result = render($empty);
				}
			}
			return $result;
		}
		public function render()
		{
			static ::$render_count++;
			Event::fire("laravel.composing: {$this->view}", array($this));
			$contents = null;
			if (Event::listeners(static ::engine)) {
				$result = Event::until(static ::engine, array($this));
				if (!is_null($result)) $contents = $result;
			}
			if (is_null($contents)) $contents = $this->get();
			static ::$render_count--;
			if (static ::$render_count == 0) {
				Section::$sections = array();
			}
			return $contents;
		}
		public function get()
		{
			$__data = $this->data();
			$__contents = $this->load();
			ob_start() and extract($__data, EXTR_SKIP);
			try {
				eval('?>' . $__contents);
			}
			catch(\Exception $e) {
				ob_get_clean();
				throw $e;
			}
			$content = ob_get_clean();
			if (Event::listeners('view.filter')) {
				return Event::first('view.filter', array($content, $this->path));
			}
			return $content;
		}
		protected function load()
		{
			static ::$last = array('name' => $this->view, 'path' => $this->path);
			if (isset(static ::$cache[$this->path])) {
				return static ::$cache[$this->path];
			} else {
				return static ::$cache[$this->path] = file_get_contents($this->path);
			}
		}
		public function data()
		{
			$data = array_merge($this->data, static ::$shared);
			foreach ($data as $key => $value) {
				if ($value instanceof View or $value instanceof Response) {
					$data[$key] = $value->render();
				}
			}
			return $data;
		}
		public function nest($key, $view, $data = array())
		{
			return $this->with($key, static ::make($view, $data));
		}
		public function with($key, $value = null)
		{
			if (is_array($key)) {
				$this->data = array_merge($this->data, $key);
			} else {
				$this->data[$key] = $value;
			}
			return $this;
		}
		public function shares($key, $value)
		{
			static ::share($key, $value);
			return $this;
		}
		public static function share($key, $value)
		{
			static ::$shared[$key] = $value;
		}
		public function offsetExists($offset)
		{
			return array_key_exists($offset, $this->data);
		}
		public function offsetGet($offset)
		{
			if (isset($this[$offset])) return $this->data[$offset];
		}
		public function offsetSet($offset, $value)
		{
			$this->data[$offset] = $value;
		}
		public function offsetUnset($offset)
		{
			unset($this->data[$offset]);
		}
		public function __get($key)
		{
			return $this->data[$key];
		}
		public function __set($key, $value)
		{
			$this->data[$key] = $value;
		}
		public function __isset($key)
		{
			return isset($this->data[$key]);
		}
		public function __toString()
		{
			return $this->render();
		}
		public function __call($method, $parameters)
		{
			if (strpos($method, 'with_') === 0) {
				$key = substr($method, 5);
				return $this->with($key, $parameters[0]);
			}
			throw new \Exception("Method [$method] is not defined on the View class.");
		}
	}
	/**
	 * laravel\response.php
	 */
	class Response
	{
		public $content;
		public $foundation;
		public function __construct($content, $status = 200, $headers = array())
		{
			$this->content = $content;
			$this->foundation = new FoundationResponse('', $status, $headers);
		}
		public static function make($content, $status = 200, $headers = array())
		{
			return new static ($content, $status, $headers);
		}
		public static function view($view, $data = array())
		{
			return new static (View::make($view, $data));
		}
		public static function json($data, $status = 200, $headers = array(), $json_options = 0)
		{
			$headers['Content-Type'] = 'application/json; charset=utf-8';
			return new static (json_encode($data, $json_options), $status, $headers);
		}
		public static function jsonp($callback, $data, $status = 200, $headers = array())
		{
			$headers['Content-Type'] = 'application/javascript; charset=utf-8';
			return new static ($callback . '(' . json_encode($data) . ');', $status, $headers);
		}
		public static function eloquent($data, $status = 200, $headers = array())
		{
			$headers['Content-Type'] = 'application/json; charset=utf-8';
			return new static (eloquent_to_json($data), $status, $headers);
		}
		public static function error($code, $data = array())
		{
			return new static (View::make('error.' . $code, $data), $code);
		}
		public static function download($path, $name = null, $headers = array())
		{
			if (is_null($name)) $name = basename($path);
			$headers = array_merge(array('Content-Description' => 'File Transfer', 'Content-Type' => File::mime(File::extension($path)), 'Content-Transfer-Encoding' => 'binary', 'Expires' => 0, 'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0', 'Pragma' => 'public', 'Content-Length' => File::size($path),), $headers);
			$response = new static (File::get($path), 200, $headers);
			$d = $response->disposition($name);
			return $response->header('Content-Disposition', $d);
		}
		public function disposition($file)
		{
			$type = ResponseHeaderBag::DISPOSITION_ATTACHMENT;
			return $this->foundation->headers->makeDisposition($type, $file);
		}
		public static function prepare($response)
		{
			if (!$response instanceof Response) {
				$response = new static ($response);
			}
			return $response;
		}
		public function send()
		{
			$this->cookies();
			$this->foundation->prepare(Request::foundation());
			$this->foundation->send();
		}
		public function render()
		{
			if (str_object($this->content)) {
				$this->content = $this->content->__toString();
			} else {
				$this->content = (string)$this->content;
			}
			$this->foundation->setContent($this->content);
			return $this->content;
		}
		public function send_headers()
		{
			$this->foundation->prepare(Request::foundation());
			$this->foundation->sendHeaders();
		}
		protected function cookies()
		{
			$ref = new \ReflectionClass('Symfony\Component\HttpFoundation\Cookie');
			foreach (Cookie::$jar as $name => $cookie) {
				$config = array_values($cookie);
				$this->headers()->setCookie($ref->newInstanceArgs($config));
			}
		}
		public function header($name, $value)
		{
			$this->foundation->headers->set($name, $value);
			return $this;
		}
		public function headers()
		{
			return $this->foundation->headers;
		}
		public function status($status = null)
		{
			if (is_null($status)) {
				return $this->foundation->getStatusCode();
			} else {
				$this->foundation->setStatusCode($status);
				return $this;
			}
		}
		public function __toString()
		{
			return $this->render();
		}
	}
	/**
	 * laravel\redirect.php
	 */
	class Redirect extends Response
	{
		public static function home($status = 302, $https = null)
		{
			return static ::to(URL::home($https), $status);
		}
		public static function back($status = 302)
		{
			return static ::to(Request::referrer(), $status);
		}
		public static function to($url, $status = 302, $https = null)
		{
			return static ::make('', $status)->header('Location', URL::to($url, $https));
		}
		public static function to_secure($url, $status = 302)
		{
			return static ::to($url, $status, true);
		}
		public static function to_action($action, $parameters = array(), $status = 302)
		{
			return static ::to(URL::to_action($action, $parameters), $status);
		}
		public static function to_route($route, $parameters = array(), $status = 302)
		{
			return static ::to(URL::to_route($route, $parameters), $status);
		}
		public function with($key, $value)
		{
			if (Config::get('session.driver') == '') {
				throw new \Exception('A session driver must be set before setting flash data.');
			}
			Session::flash($key, $value);
			return $this;
		}
		public function with_input($filter = null, $items = array())
		{
			Input::flash($filter, $items);
			return $this;
		}
		public function with_errors($container)
		{
			$errors = ($container instanceof Validator) ? $container->errors : $container;
			return $this->with('errors', $errors);
		}
		public function send()
		{
			while (ob_get_level() > 0) {
				ob_end_clean();
			}
			return parent::send();
		}
	}
	/**
	 * laravel\auth.php
	 */
	class Auth
	{
		public static $drivers = array();
		public static $registrar = array();
		public static function driver($driver = null)
		{
			if (is_null($driver)) $driver = Config::get('auth.driver');
			if (!isset(static ::$drivers[$driver])) {
				static ::$drivers[$driver] = static ::factory($driver);
			}
			return static ::$drivers[$driver];
		}
		protected static function factory($driver)
		{
			if (isset(static ::$registrar[$driver])) {
				$resolver = static ::$registrar[$driver];
				return $resolver();
			}
			switch ($driver) {
			case 'fluent':
				return new Auth\Drivers\Fluent(Config::get('auth.table'));
			case 'eloquent':
				return new Auth\Drivers\Eloquent(Config::get('auth.model'));
			default:
				throw new \Exception("Auth driver {$driver} is not supported.");
			}
		}
		public static function extend($driver, Closure $resolver)
		{
			static ::$registrar[$driver] = $resolver;
		}
		public static function __callStatic($method, $parameters)
		{
			return call_user_func_array(array(static ::driver(), $method), $parameters);
		}
	}
	/**
	 * laravel\autoloader.php
	 */
	class Autoloader
	{
		public static $mappings = array();
		public static $directories = array();
		public static $namespaces = array();
		public static $underscored = array();
		public static $aliases = array();
		public static function load($class)
		{
			if (isset(static ::$aliases[$class])) {
				return class_alias(static ::$aliases[$class], $class);
			} elseif (isset(static ::$mappings[$class])) {
				require static ::$mappings[$class];
				return;
			}
			foreach (static ::$namespaces as $namespace => $directory) {
				if (starts_with($class, $namespace)) {
					return static ::load_namespaced($class, $namespace, $directory);
				}
			}
			static ::load_psr($class);
		}
		protected static function load_namespaced($class, $namespace, $directory)
		{
			return static ::load_psr(substr($class, strlen($namespace)), $directory);
		}
		protected static function load_psr($class, $directory = null)
		{
			$file = str_replace(array('\\', '_'), '/', $class);
			$directories = $directory ? : static ::$directories;
			$lower = strtolower($file);
			foreach ((array)$directories as $directory) {
				if (file_exists($path = $directory . $lower . EXT)) {
					return require $path;
				} elseif (file_exists($path = $directory . $file . EXT)) {
					return require $path;
				}
			}
		}
		public static function map($mappings)
		{
			static ::$mappings = array_merge(static ::$mappings, $mappings);
		}
		public static function alias($class, $alias)
		{
			static ::$aliases[$alias] = $class;
		}
		public static function directories($directory)
		{
			$directories = static ::format($directory);
			static ::$directories = array_unique(array_merge(static ::$directories, $directories));
		}
		public static function namespaces($mappings, $append = '\\')
		{
			$mappings = static ::format_mappings($mappings, $append);
			static ::$namespaces = array_merge($mappings, static ::$namespaces);
		}
		public static function underscored($mappings)
		{
			static ::namespaces($mappings, '_');
		}
		protected static function format_mappings($mappings, $append)
		{
			foreach ($mappings as $namespace => $directory) {
				$namespace = trim($namespace, $append) . $append;
				unset(static ::$namespaces[$namespace]);
				$namespaces[$namespace] = head(static ::format($directory));
			}
			return $namespaces;
		}
		protected static function format($directories)
		{
			return array_map(function ($directory)
			{
				return rtrim($directory, DS) . DS;
			}, (array)$directories);
		}
	}
	/**
	 * laravel\blade.php
	 */
	class Blade
	{
		protected static $compilers = array('extensions', 'layouts', 'comments', 'echos', 'forelse', 'empty', 'endforelse', 'structure_openings', 'structure_closings', 'else', 'unless', 'endunless', 'includes', 'render_each', 'render', 'yields', 'yield_sections', 'section_start', 'section_end',);
		protected static $extensions = array();
		public static function sharpen()
		{
			Event::listen(View::engine, function ($view)
			{
				if (!str_contains($view->path, BLADE_EXT)) {
					return;
				}
				$compiled = Blade::compiled($view->path);
				if (!file_exists($compiled) or Blade::expired($view->view, $view->path)) {
					file_put_contents($compiled, Blade::compile($view));
				}
				$view->path = $compiled;
				return ltrim($view->get());
			});
		}
		public static function extend(Closure $compiler)
		{
			static ::$extensions[] = $compiler;
		}
		public static function expired($view, $path)
		{
			return filemtime($path) > filemtime(static ::compiled($path));
		}
		public static function compile($view)
		{
			return static ::compile_string(file_get_contents($view->path), $view);
		}
		public static function compile_string($value, $view = null)
		{
			foreach (static ::$compilers as $compiler) {
				$method = "compile_{$compiler}";
				$value = static ::$method($value, $view);
			}
			return $value;
		}
		protected static function compile_layouts($value)
		{
			if (!starts_with($value, '@layout')) {
				return $value;
			}
			$lines = preg_split("/(\r?\n)/", $value);
			$pattern = static ::matcher('layout');
			$lines[] = preg_replace($pattern, '$1@include$2', $lines[0]);
			return implode(CRLF, array_slice($lines, 1));
		}
		protected static function extract($value, $expression)
		{
			preg_match('/@layout(\s*\(.*\))(\s*)/', $value, $matches);
			return str_replace(array("('", "')"), '', $matches[1]);
		}
		protected static function compile_comments($value)
		{
			$value = preg_replace('/\{\{--(.+?)(--\}\})?\n/', "<?php // $1 ?>", $value);
			return preg_replace('/\{\{--((.|\s)*?)--\}\}/', "<?php /* $1 */ ?>\n", $value);
		}
		protected static function compile_echos($value)
		{
			$value = preg_replace('/\{\{\{(.+?)\}\}\}/', '<?php echo HTML::entities($1); ?>', $value);
			return preg_replace('/\{\{(.+?)\}\}/', '<?php echo $1; ?>', $value);
		}
		protected static function compile_forelse($value)
		{
			preg_match_all('/(\s*)@forelse(\s*\(.*\))(\s*)/', $value, $matches);
			foreach ($matches[0] as $forelse) {
				preg_match('/\s*\(\s*(\S*)\s/', $forelse, $variable);
				$if = "<?php if (count({$variable[1]}) > 0): ?>";
				$search = '/(\s*)@forelse(\s*\(.*\))/';
				$replace = '$1' . $if . '<?php foreach$2: ?>';
				$blade = preg_replace($search, $replace, $forelse);
				$value = str_replace($forelse, $blade, $value);
			}
			return $value;
		}
		protected static function compile_empty($value)
		{
			return str_replace('@empty', '<?php endforeach; ?><?php else: ?>', $value);
		}
		protected static function compile_endforelse($value)
		{
			return str_replace('@endforelse', '<?php endif; ?>', $value);
		}
		protected static function compile_structure_openings($value)
		{
			$pattern = '/(\s*)@(if|elseif|foreach|for|while)(\s*\(.*\))/';
			return preg_replace($pattern, '$1<?php $2$3: ?>', $value);
		}
		protected static function compile_structure_closings($value)
		{
			$pattern = '/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/';
			return preg_replace($pattern, '$1<?php $2; ?>$3', $value);
		}
		protected static function compile_else($value)
		{
			return preg_replace('/(\s*)@(else)(\s*)/', '$1<?php $2: ?>$3', $value);
		}
		protected static function compile_unless($value)
		{
			$pattern = '/(\s*)@unless(\s*\(.*\))/';
			return preg_replace($pattern, '$1<?php if ( ! ($2)): ?>', $value);
		}
		protected static function compile_endunless($value)
		{
			return str_replace('@endunless', '<?php endif; ?>', $value);
		}
		protected static function compile_includes($value)
		{
			$pattern = static ::matcher('include');
			return preg_replace($pattern, '$1<?php echo view$2->with(get_defined_vars())->render(); ?>', $value);
		}
		protected static function compile_render($value)
		{
			$pattern = static ::matcher('render');
			return preg_replace($pattern, '$1<?php echo render$2; ?>', $value);
		}
		protected static function compile_render_each($value)
		{
			$pattern = static ::matcher('render_each');
			return preg_replace($pattern, '$1<?php echo render_each$2; ?>', $value);
		}
		protected static function compile_yields($value)
		{
			$pattern = static ::matcher('yield');
			return preg_replace($pattern, '$1<?php echo \\Laravel\\Section::yield$2; ?>', $value);
		}
		protected static function compile_yield_sections($value)
		{
			$replace = '<?php echo \\Laravel\\Section::yield_section(); ?>';
			return str_replace('@yield_section', $replace, $value);
		}
		protected static function compile_section_start($value)
		{
			$pattern = static ::matcher('section');
			return preg_replace($pattern, '$1<?php \\Laravel\\Section::start$2; ?>', $value);
		}
		protected static function compile_section_end($value)
		{
			return preg_replace('/@endsection/', '<?php \\Laravel\\Section::stop(); ?>', $value);
		}
		protected static function compile_extensions($value)
		{
			foreach (static ::$extensions as $compiler) {
				$value = $compiler($value);
			}
			return $value;
		}
		public static function matcher($function)
		{
			return '/(\s*)@' . $function . '(\s*\(.*\))/';
		}
		public static function compiled($path)
		{
			return path('storage') . 'views/' . md5($path);
		}
	}
	/**
	 * laravel\bundle.php
	 */
	defined('DS') or die('No direct script access.');
	class Bundle
	{
		public static $bundles = array();
		public static $elements = array();
		public static $started = array();
		public static $routed = array();
		public static function register($bundle, $config = array())
		{
			$defaults = array('handles' => null, 'auto' => false);
			if (is_string($config)) {
				$bundle = $config;
				$config = array('location' => $bundle);
			}
			if (!isset($config['location'])) {
				$config['location'] = $bundle;
			}
			static ::$bundles[$bundle] = array_merge($defaults, $config);
			if (isset($config['autoloads'])) {
				static ::autoloads($bundle, $config);
			}
		}
		public static function start($bundle)
		{
			if (static ::started($bundle)) return;
			if (!static ::exists($bundle)) {
				throw new \Exception("Bundle [$bundle] has not been installed.");
			}
			if (!is_null($starter = static ::option($bundle, 'starter'))) {
				$starter();
			} elseif (file_exists($path = static ::path($bundle) . 'start' . EXT)) {
				require $path;
			}
			static ::routes($bundle);
			Event::fire("laravel.started: {$bundle}");
			static ::$started[] = strtolower($bundle);
		}
		public static function routes($bundle)
		{
			if (static ::routed($bundle)) return;
			$path = static ::path($bundle) . 'routes' . EXT;
			Router::$bundle = static ::option($bundle, 'handles');
			if (!static ::routed($bundle) and file_exists($path)) {
				static ::$routed[] = $bundle;
				require $path;
			}
		}
		protected static function autoloads($bundle, $config)
		{
			$path = rtrim(Bundle::path($bundle), DS);
			foreach ($config['autoloads'] as $type => $mappings) {
				$mappings = array_map(function ($mapping) use ($path)
				{
					return str_replace('(:bundle)', $path, $mapping);
				}, $mappings);
				Autoloader::$type($mappings);
			}
		}
		public static function disable($bundle)
		{
			unset(static ::$bundles[$bundle]);
		}
		public static function handles($uri)
		{
			$uri = rtrim($uri, '/') . '/';
			foreach (static ::$bundles as $key => $value) {
				if (isset($value['handles']) and starts_with($uri, $value['handles'] . '/') or $value['handles'] == '/') {
					return $key;
				}
			}
			return DEFAULT_BUNDLE;
		}
		public static function exists($bundle)
		{
			return $bundle == DEFAULT_BUNDLE or in_array(strtolower($bundle), static ::names());
		}
		public static function started($bundle)
		{
			return in_array(strtolower($bundle), static ::$started);
		}
		public static function routed($bundle)
		{
			return in_array(strtolower($bundle), static ::$routed);
		}
		public static function prefix($bundle)
		{
			return ($bundle !== DEFAULT_BUNDLE) ? "{$bundle}::" : '';
		}
		public static function class_prefix($bundle)
		{
			return ($bundle !== DEFAULT_BUNDLE) ? Str::classify($bundle) . '_' : '';
		}
		public static function path($bundle)
		{
			if (is_null($bundle) or $bundle === DEFAULT_BUNDLE) {
				return path('app');
			} elseif ($location = array_get(static ::$bundles, $bundle . '.location')) {
				if (starts_with($location, 'path: ')) {
					return str_finish(substr($location, 6), DS);
				} else {
					return str_finish(path('bundle') . $location, DS);
				}
			}
		}
		public static function assets($bundle)
		{
			if (is_null($bundle)) return static ::assets(DEFAULT_BUNDLE);
			return ($bundle != DEFAULT_BUNDLE) ? "/bundles/{$bundle}/" : '/';
		}
		public static function name($identifier)
		{
			list($bundle, $element) = static ::parse($identifier);
			return $bundle;
		}
		public static function element($identifier)
		{
			list($bundle, $element) = static ::parse($identifier);
			return $element;
		}
		public static function identifier($bundle, $element)
		{
			return (is_null($bundle) or $bundle == DEFAULT_BUNDLE) ? $element : $bundle . '::' . $element;
		}
		public static function resolve($bundle)
		{
			return (static ::exists($bundle)) ? $bundle : DEFAULT_BUNDLE;
		}
		public static function parse($identifier)
		{
			if (isset(static ::$elements[$identifier])) {
				return static ::$elements[$identifier];
			}
			if (strpos($identifier, '::') !== false) {
				$element = explode('::', strtolower($identifier));
			} else {
				$element = array(DEFAULT_BUNDLE, strtolower($identifier));
			}
			return static ::$elements[$identifier] = $element;
		}
		public static function get($bundle)
		{
			return array_get(static ::$bundles, $bundle);
		}
		public static function option($bundle, $option, $default = null)
		{
			$bundle = static ::get($bundle);
			if (is_null($bundle)) {
				return value($default);
			}
			return array_get($bundle, $option, $default);
		}
		public static function all()
		{
			return static ::$bundles;
		}
		public static function names()
		{
			return array_keys(static ::$bundles);
		}
		public static function expand($path)
		{
			list($bundle, $element) = static ::parse($path);
			return static ::path($bundle) . $element;
		}
	}
	/**
	 * laravel\cache.php
	 */
	class Cache
	{
		public static $drivers = array();
		public static $registrar = array();
		public static function driver($driver = null)
		{
			if (is_null($driver)) $driver = Config::get('cache.driver');
			if (!isset(static ::$drivers[$driver])) {
				static ::$drivers[$driver] = static ::factory($driver);
			}
			return static ::$drivers[$driver];
		}
		protected static function factory($driver)
		{
			if (isset(static ::$registrar[$driver])) {
				$resolver = static ::$registrar[$driver];
				return $resolver();
			}
			switch ($driver) {
			case 'apc':
				return new Cache\Drivers\APC(Config::get('cache.key'));
			case 'file':
				return new Cache\Drivers\File(path('storage') . 'cache' . DS);
			case 'memcached':
				return new Cache\Drivers\Memcached(Memcached::connection(), Config::get('cache.key'));
			case 'memory':
				return new Cache\Drivers\Memory;
			case 'redis':
				return new Cache\Drivers\Redis(Redis::db());
			case 'database':
				return new Cache\Drivers\Database(Config::get('cache.key'));
			case 'wincache':
				return new Cache\Drivers\WinCache(Config::get('cache.key'));
			default:
				throw new \Exception("Cache driver {$driver} is not supported.");
			}
		}
		public static function extend($driver, Closure $resolver)
		{
			static ::$registrar[$driver] = $resolver;
		}
		public static function __callStatic($method, $parameters)
		{
			return call_user_func_array(array(static ::driver(), $method), $parameters);
		}
	}
	/**
	 * laravel\config.php
	 */
	defined('DS') or die('No direct script access.');
	class Config
	{
		public static $items = array();
		public static $cache = array();
		const loader = 'laravel.config.loader';
		public static function has($key)
		{
			return !is_null(static ::get($key));
		}
		public static function get($key, $default = null)
		{
			list($bundle, $file, $item) = static ::parse($key);
			if (!static ::load($bundle, $file)) return value($default);
			$items = static ::$items[$bundle][$file];
			if (is_null($item)) {
				return $items;
			} else {
				return array_get($items, $item, $default);
			}
		}
		public static function set($key, $value)
		{
			list($bundle, $file, $item) = static ::parse($key);
			static ::load($bundle, $file);
			if (is_null($item)) {
				array_set(static ::$items[$bundle], $file, $value);
			} else {
				array_set(static ::$items[$bundle][$file], $item, $value);
			}
		}
		protected static function parse($key)
		{
			if (array_key_exists($key, static ::$cache)) {
				return static ::$cache[$key];
			}
			$bundle = Bundle::name($key);
			$segments = explode('.', Bundle::element($key));
			if (count($segments) >= 2) {
				$parsed = array($bundle, $segments[0], implode('.', array_slice($segments, 1)));
			} else {
				$parsed = array($bundle, $segments[0], null);
			}
			return static ::$cache[$key] = $parsed;
		}
		public static function load($bundle, $file)
		{
			if (isset(static ::$items[$bundle][$file])) return true;
			$config = Event::first(static ::loader, func_get_args());
			if (count($config) > 0) {
				static ::$items[$bundle][$file] = $config;
			}
			return isset(static ::$items[$bundle][$file]);
		}
		public static function file($bundle, $file)
		{
			$config = array();
			foreach (static ::paths($bundle) as $directory) {
				if ($directory !== '' and file_exists($path = $directory . $file . EXT)) {
					$config = array_merge($config, require $path);
				}
			}
			return $config;
		}
		protected static function paths($bundle)
		{
			$paths[] = Bundle::path($bundle) . 'config/';
			if (!is_null(Request::env())) {
				$paths[] = $paths[count($paths) - 1] . Request::env() . '/';
			}
			return $paths;
		}
	}
	/**
	 * laravel\cookie.php
	 */
	class Cookie
	{
		const forever = 2628000;
		public static $jar = array();
		public static function has($name)
		{
			return !is_null(static ::get($name));
		}
		public static function get($name, $default = null)
		{
			if (isset(static ::$jar[$name])) return static ::parse(static ::$jar[$name]['value']);
			if (!is_null($value = Request::foundation()->cookies->get($name))) {
				return static ::parse($value);
			}
			return value($default);
		}
		public static function put($name, $value, $expiration = 0, $path = '/', $domain = null, $secure = false)
		{
			if ($expiration !== 0) {
				$expiration = time() + ($expiration * 60);
			}
			$value = static ::hash($value) . '+' . $value;
			if ($secure and !Request::secure()) {
				throw new \Exception("Attempting to set secure cookie over HTTP.");
			}
			static ::$jar[$name] = compact('name', 'value', 'expiration', 'path', 'domain', 'secure');
		}
		public static function forever($name, $value, $path = '/', $domain = null, $secure = false)
		{
			return static ::put($name, $value, static ::forever, $path, $domain, $secure);
		}
		public static function forget($name, $path = '/', $domain = null, $secure = false)
		{
			return static ::put($name, null, -2000, $path, $domain, $secure);
		}
		public static function hash($value)
		{
			return hash_hmac('sha1', $value, Config::get('application.key'));
		}
		protected static function parse($value)
		{
			$segments = explode('+', $value);
			if (!(count($segments) >= 2)) {
				return null;
			}
			$value = implode('+', array_slice($segments, 1));
			if ($segments[0] == static ::hash($value)) {
				return $value;
			}
			return null;
		}
	}
	/**
	 * laravel\core.php
	 */
	define('EXT', '.php');
	define('CRLF', "\r\n");
	define('BLADE_EXT', '.blade.php');
	define('DEFAULT_BUNDLE', 'application');
	define('MB_STRING', (int)function_exists('mb_get_info'));
	ob_start('mb_output_handler');
	spl_autoload_register(array('Laravel\\Autoloader', 'load'));
	if (magic_quotes()) {
		$magics = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		foreach ($magics as & $magic) {
			$magic = array_strip_slashes($magic);
		}
	}
	Request::$foundation = RequestFoundation::createFromGlobals();
	if (Request::cli()) {
		$environment = get_cli_option('env', getenv('LARAVEL_ENV'));
		if (empty($environment)) {
			$environment = Request::detect_env($environments, gethostname());
		}
	} else {
		$root = Request::foundation()->getRootUrl();
		$environment = Request::detect_env($environments, $root);
	}
	if (isset($environment)) {
		Request::set_env($environment);
	}
	if (Request::cli()) {
		$console = CLI\Command::options($_SERVER['argv']);
		list($arguments, $options) = $console;
		$options = array_change_key_case($options, CASE_UPPER);
		$_SERVER['CLI'] = $options;
	}
	$bundles = require path('app') . 'bundles' . EXT;
	foreach ($bundles as $bundle => $config) {
		Bundle::register($bundle, $config);
	}
	/**
	 * laravel\crypter.php
	 */
	defined('DS') or die('No direct script access.');
	class Crypter
	{
		public static $cipher = MCRYPT_RIJNDAEL_256;
		public static $mode = MCRYPT_MODE_CBC;
		public static $block = 32;
		public static function encrypt($value)
		{
			$iv = mcrypt_create_iv(static ::iv_size(), static ::randomizer());
			$value = static ::pad($value);
			$value = mcrypt_encrypt(static ::$cipher, static ::key(), $value, static ::$mode, $iv);
			return base64_encode($iv . $value);
		}
		public static function decrypt($value)
		{
			$value = base64_decode($value);
			$iv = substr($value, 0, static ::iv_size());
			$value = substr($value, static ::iv_size());
			$key = static ::key();
			$value = mcrypt_decrypt(static ::$cipher, $key, $value, static ::$mode, $iv);
			return static ::unpad($value);
		}
		public static function randomizer()
		{
			if (defined('MCRYPT_DEV_URANDOM')) {
				return MCRYPT_DEV_URANDOM;
			} elseif (defined('MCRYPT_DEV_RANDOM')) {
				return MCRYPT_DEV_RANDOM;
			} else {
				mt_srand();
				return MCRYPT_RAND;
			}
		}
		protected static function iv_size()
		{
			return mcrypt_get_iv_size(static ::$cipher, static ::$mode);
		}
		protected static function pad($value)
		{
			$pad = static ::$block - (Str::length($value) % static ::$block);
			return $value.= str_repeat(chr($pad), $pad);
		}
		protected static function unpad($value)
		{
			if (MB_STRING) {
				$pad = ord(mb_substr($value, -1, 1, Config::get('application.encoding')));
			} else {
				$pad = ord(substr($value, -1));
			}
			if ($pad and $pad < static ::$block) {
				if (preg_match('/' . chr($pad) . '{' . $pad . '}$/', $value)) {
					if (MB_STRING) {
						return mb_substr($value, 0, Str::length($value) - $pad, Config::get('application.encoding'));
					}
					return substr($value, 0, Str::length($value) - $pad);
				} else {
					throw new \Exception("Decryption error. Padding is invalid.");
				}
			}
			return $value;
		}
		protected static function key()
		{
			return Config::get('application.key');
		}
	}
	/**
	 * laravel\database.php
	 */
	class Database
	{
		public static $connections = array();
		public static $registrar = array();
		public static function connection($connection = null)
		{
			if (is_null($connection)) $connection = Config::get('database.default');
			if (!isset(static ::$connections[$connection])) {
				$config = Config::get("database.connections.{$connection}");
				if (is_null($config)) {
					throw new \Exception("Database connection is not defined for [$connection].");
				}
				static ::$connections[$connection] = new Connection(static ::connect($config), $config);
			}
			return static ::$connections[$connection];
		}
		protected static function connect($config)
		{
			return static ::connector($config['driver'])->connect($config);
		}
		protected static function connector($driver)
		{
			if (isset(static ::$registrar[$driver])) {
				$resolver = static ::$registrar[$driver]['connector'];
				return $resolver();
			}
			switch ($driver) {
			case 'sqlite':
				return new Database\Connectors\SQLite;
			case 'mysql':
				return new Database\Connectors\MySQL;
			case 'pgsql':
				return new Database\Connectors\Postgres;
			case 'sqlsrv':
				return new Database\Connectors\SQLServer;
			default:
				throw new \Exception("Database driver [$driver] is not supported.");
			}
		}
		public static function table($table, $connection = null)
		{
			return static ::connection($connection)->table($table);
		}
		public static function raw($value)
		{
			return new Expression($value);
		}
		public static function escape($value)
		{
			return static ::connection()->pdo->quote($value);
		}
		public static function profile()
		{
			return Database\Connection::$queries;
		}
		public static function last_query()
		{
			return end(Database\Connection::$queries);
		}
		public static function extend($name, Closure $connector, $query = null, $schema = null)
		{
			if (is_null($query)) $query = '\Laravel\Database\Query\Grammars\Grammar';
			static ::$registrar[$name] = compact('connector', 'query', 'schema');
		}
		public static function __callStatic($method, $parameters)
		{
			return call_user_func_array(array(static ::connection(), $method), $parameters);
		}
	}
	/**
	 * laravel\error.php
	 */
	class Error
	{
		public static function exception($exception, $trace = true)
		{
			static ::log($exception);
			ob_get_level() and ob_end_clean();
			$message = $exception->getMessage();
			$file = $exception->getFile();
			if (str_contains($exception->getFile(), 'eval()') and str_contains($exception->getFile(), 'laravel' . DS . 'view.php')) {
				$message = 'Error rendering view: [' . View::$last['name'] . ']' . PHP_EOL . PHP_EOL . $message;
				$file = View::$last['path'];
			}
			if (Config::get('error.detail')) {
				$response_body = "<html><h2>Unhandled Exception</h2>
				<h3>Message:</h3>
				<pre>" . $message . "</pre>
				<h3>Location:</h3>
				<pre>" . $file . " on line " . $exception->getLine() . "</pre>";
				if ($trace) {
					$response_body.= "
				  <h3>Stack Trace:</h3>
				  <pre>" . $exception->getTraceAsString() . "</pre></html>";
				}
				$response = Response::make($response_body, 500);
			} else {
				$response = Event::first('500');
				$response = Response::prepare($response);
			}
			$response->render();
			$response->send();
			$response->foundation->finish();
			exit(1);
		}
		public static function native($code, $error, $file, $line)
		{
			if (error_reporting() === 0) return;
			$exception = new \ErrorException($error, $code, 0, $file, $line);
			if (in_array($code, Config::get('error.ignore'))) {
				return static ::log($exception);
			}
			static ::exception($exception);
		}
		public static function shutdown()
		{
			$error = error_get_last();
			if (!is_null($error)) {
				extract($error, EXTR_SKIP);
				static ::exception(new \ErrorException($message, $type, 0, $file, $line), false);
			}
		}
		public static function log($exception)
		{
			if (Config::get('error.log')) {
				call_user_func(Config::get('error.logger'), $exception);
			}
		}
	}
	/**
	 * laravel\event.php
	 */
	class Event
	{
		public static $events = array();
		public static $queued = array();
		public static $flushers = array();
		public static function listeners($event)
		{
			return isset(static ::$events[$event]);
		}
		public static function listen($event, $callback)
		{
			static ::$events[$event][] = $callback;
		}
		public static function override($event, $callback)
		{
			static ::clear($event);
			static ::listen($event, $callback);
		}
		public static function queue($queue, $key, $data = array())
		{
			static ::$queued[$queue][$key] = $data;
		}
		public static function flusher($queue, $callback)
		{
			static ::$flushers[$queue][] = $callback;
		}
		public static function clear($event)
		{
			unset(static ::$events[$event]);
		}
		public static function first($event, $parameters = array())
		{
			return head(static ::fire($event, $parameters));
		}
		public static function until($event, $parameters = array())
		{
			return static ::fire($event, $parameters, true);
		}
		public static function flush($queue)
		{
			foreach (static ::$flushers[$queue] as $flusher) {
				if (!isset(static ::$queued[$queue])) continue;
				foreach (static ::$queued[$queue] as $key => $payload) {
					array_unshift($payload, $key);
					call_user_func_array($flusher, $payload);
				}
			}
		}
		public static function fire($events, $parameters = array(), $halt = false)
		{
			$responses = array();
			$parameters = (array)$parameters;
			foreach ((array)$events as $event) {
				if (static ::listeners($event)) {
					foreach (static ::$events[$event] as $callback) {
						$response = call_user_func_array($callback, $parameters);
						if ($halt and !is_null($response)) {
							return $response;
						}
						$responses[] = $response;
					}
				}
			}
			return $halt ? null : $responses;
		}
	}
	/**
	 * laravel\file.php
	 */
	class File
	{
		public static function exists($path)
		{
			return file_exists($path);
		}
		public static function get($path, $default = null)
		{
			return (file_exists($path)) ? file_get_contents($path) : value($default);
		}
		public static function put($path, $data)
		{
			return file_put_contents($path, $data, LOCK_EX);
		}
		public static function append($path, $data)
		{
			return file_put_contents($path, $data, LOCK_EX | FILE_APPEND);
		}
		public static function delete($path)
		{
			if (static ::exists($path)) return @unlink($path);
		}
		public static function move($path, $target)
		{
			return rename($path, $target);
		}
		public static function copy($path, $target)
		{
			return copy($path, $target);
		}
		public static function extension($path)
		{
			return pathinfo($path, PATHINFO_EXTENSION);
		}
		public static function type($path)
		{
			return filetype($path);
		}
		public static function size($path)
		{
			return filesize($path);
		}
		public static function modified($path)
		{
			return filemtime($path);
		}
		public static function mime($extension, $default = 'application/octet-stream')
		{
			$mimes = Config::get('mimes');
			if (!array_key_exists($extension, $mimes)) return $default;
			return (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
		}
		public static function is($extensions, $path)
		{
			$mimes = Config::get('mimes');
			$mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
			foreach ((array)$extensions as $extension) {
				if (isset($mimes[$extension]) and in_array($mime, (array)$mimes[$extension])) {
					return true;
				}
			}
			return false;
		}
		public static function mkdir($path, $chmod = 0777)
		{
			return (!is_dir($path)) ? mkdir($path, $chmod, true) : true;
		}
		public static function mvdir($source, $destination, $options = fIterator::SKIP_DOTS)
		{
			return static ::cpdir($source, $destination, true, $options);
		}
		public static function cpdir($source, $destination, $delete = false, $options = fIterator::SKIP_DOTS)
		{
			if (!is_dir($source)) return false;
			if (!is_dir($destination)) {
				mkdir($destination, 0777, true);
			}
			$items = new fIterator($source, $options);
			foreach ($items as $item) {
				$location = $destination . DS . $item->getBasename();
				if ($item->isDir()) {
					$path = $item->getRealPath();
					if (!static ::cpdir($path, $location, $delete, $options)) return false;
					if ($delete) @rmdir($item->getRealPath());
				} else {
					if (!copy($item->getRealPath(), $location)) return false;
					if ($delete) @unlink($item->getRealPath());
				}
			}
			unset($items);
			if ($delete) @rmdir($source);
			return true;
		}
		public static function rmdir($directory, $preserve = false)
		{
			if (!is_dir($directory)) return;
			$items = new fIterator($directory);
			foreach ($items as $item) {
				if ($item->isDir()) {
					static ::rmdir($item->getRealPath());
				} else {
					@unlink($item->getRealPath());
				}
			}
			unset($items);
			if (!$preserve) @rmdir($directory);
		}
		public static function cleandir($directory)
		{
			return static ::rmdir($directory, true);
		}
		public static function latest($directory, $options = fIterator::SKIP_DOTS)
		{
			$latest = null;
			$time = 0;
			$items = new fIterator($directory, $options);
			foreach ($items as $item) {
				if ($item->getMTime() > $time) {
					$latest = $item;
					$time = $item->getMTime();
				}
			}
			return $latest;
		}
	}
	/**
	 * laravel\fluent.php
	 */
	class Fluent
	{
		public $attributes = array();
		public function __construct($attributes = array())
		{
			foreach ($attributes as $key => $value) {
				$this->$key = $value;
			}
		}
		public function get($attribute, $default = null)
		{
			return array_get($this->attributes, $attribute, $default);
		}
		public function __call($method, $parameters)
		{
			$this->$method = (count($parameters) > 0) ? $parameters[0] : true;
			return $this;
		}
		public function __get($key)
		{
			if (array_key_exists($key, $this->attributes)) {
				return $this->attributes[$key];
			}
		}
		public function __set($key, $value)
		{
			$this->attributes[$key] = $value;
		}
		public function __isset($key)
		{
			return isset($this->attributes[$key]);
		}
		public function __unset($key)
		{
			unset($this->attributes[$key]);
		}
	}
	/**
	 * laravel\form.php
	 */
	class Form
	{
		public static $labels = array();
		public static $macros = array();
		public static function macro($name, $macro)
		{
			static ::$macros[$name] = $macro;
		}
		public static function open($action = null, $method = 'POST', $attributes = array(), $https = null)
		{
			$method = strtoupper($method);
			$attributes['method'] = static ::method($method);
			$attributes['action'] = static ::action($action, $https);
			if (!array_key_exists('accept-charset', $attributes)) {
				$attributes['accept-charset'] = Config::get('application.encoding');
			}
			$append = '';
			if ($method == 'PUT' or $method == 'DELETE') {
				$append = static ::hidden(Request::spoofer, $method);
			}
			return '<form' . HTML::attributes($attributes) . '>' . $append;
		}
		protected static function method($method)
		{
			return ($method !== 'GET') ? 'POST' : $method;
		}
		protected static function action($action, $https)
		{
			$uri = (is_null($action)) ? URI::current() : $action;
			return HTML::entities(URL::to($uri, $https));
		}
		public static function open_secure($action = null, $method = 'POST', $attributes = array())
		{
			return static ::open($action, $method, $attributes, true);
		}
		public static function open_for_files($action = null, $method = 'POST', $attributes = array(), $https = null)
		{
			$attributes['enctype'] = 'multipart/form-data';
			return static ::open($action, $method, $attributes, $https);
		}
		public static function open_secure_for_files($action = null, $method = 'POST', $attributes = array())
		{
			return static ::open_for_files($action, $method, $attributes, true);
		}
		public static function close()
		{
			return '</form>';
		}
		public static function token()
		{
			return static ::input('hidden', Session::csrf_token, Session::token());
		}
		public static function label($name, $value, $attributes = array())
		{
			static ::$labels[] = $name;
			$attributes = HTML::attributes($attributes);
			$value = HTML::entities($value);
			return '<label for="' . $name . '"' . $attributes . '>' . $value . '</label>';
		}
		public static function input($type, $name, $value = null, $attributes = array())
		{
			$name = (isset($attributes['name'])) ? $attributes['name'] : $name;
			$id = static ::id($name, $attributes);
			$attributes = array_merge($attributes, compact('type', 'name', 'value', 'id'));
			return '<input' . HTML::attributes($attributes) . '>';
		}
		public static function text($name, $value = null, $attributes = array())
		{
			return static ::input('text', $name, $value, $attributes);
		}
		public static function password($name, $attributes = array())
		{
			return static ::input('password', $name, null, $attributes);
		}
		public static function hidden($name, $value = null, $attributes = array())
		{
			return static ::input('hidden', $name, $value, $attributes);
		}
		public static function search($name, $value = null, $attributes = array())
		{
			return static ::input('search', $name, $value, $attributes);
		}
		public static function email($name, $value = null, $attributes = array())
		{
			return static ::input('email', $name, $value, $attributes);
		}
		public static function telephone($name, $value = null, $attributes = array())
		{
			return static ::input('tel', $name, $value, $attributes);
		}
		public static function url($name, $value = null, $attributes = array())
		{
			return static ::input('url', $name, $value, $attributes);
		}
		public static function number($name, $value = null, $attributes = array())
		{
			return static ::input('number', $name, $value, $attributes);
		}
		public static function date($name, $value = null, $attributes = array())
		{
			return static ::input('date', $name, $value, $attributes);
		}
		public static function file($name, $attributes = array())
		{
			return static ::input('file', $name, null, $attributes);
		}
		public static function textarea($name, $value = '', $attributes = array())
		{
			$attributes['name'] = $name;
			$attributes['id'] = static ::id($name, $attributes);
			if (!isset($attributes['rows'])) $attributes['rows'] = 10;
			if (!isset($attributes['cols'])) $attributes['cols'] = 50;
			return '<textarea' . HTML::attributes($attributes) . '>' . HTML::entities($value) . '</textarea>';
		}
		public static function select($name, $options = array(), $selected = null, $attributes = array())
		{
			$attributes['id'] = static ::id($name, $attributes);
			$attributes['name'] = $name;
			$html = array();
			foreach ($options as $value => $display) {
				if (is_array($display)) {
					$html[] = static ::optgroup($display, $value, $selected);
				} else {
					$html[] = static ::option($value, $display, $selected);
				}
			}
			return '<select' . HTML::attributes($attributes) . '>' . implode('', $html) . '</select>';
		}
		protected static function optgroup($options, $label, $selected)
		{
			$html = array();
			foreach ($options as $value => $display) {
				$html[] = static ::option($value, $display, $selected);
			}
			return '<optgroup label="' . HTML::entities($label) . '">' . implode('', $html) . '</optgroup>';
		}
		protected static function option($value, $display, $selected)
		{
			if (is_array($selected)) {
				$selected = (in_array($value, $selected)) ? 'selected' : null;
			} else {
				$selected = ((string)$value == (string)$selected) ? 'selected' : null;
			}
			$attributes = array('value' => HTML::entities($value), 'selected' => $selected);
			return '<option' . HTML::attributes($attributes) . '>' . HTML::entities($display) . '</option>';
		}
		public static function checkbox($name, $value = 1, $checked = false, $attributes = array())
		{
			return static ::checkable('checkbox', $name, $value, $checked, $attributes);
		}
		public static function radio($name, $value = null, $checked = false, $attributes = array())
		{
			if (is_null($value)) $value = $name;
			return static ::checkable('radio', $name, $value, $checked, $attributes);
		}
		protected static function checkable($type, $name, $value, $checked, $attributes)
		{
			if ($checked) $attributes['checked'] = 'checked';
			$attributes['id'] = static ::id($name, $attributes);
			return static ::input($type, $name, $value, $attributes);
		}
		public static function submit($value = null, $attributes = array())
		{
			return static ::input('submit', null, $value, $attributes);
		}
		public static function reset($value = null, $attributes = array())
		{
			return static ::input('reset', null, $value, $attributes);
		}
		public static function image($url, $name = null, $attributes = array())
		{
			$attributes['src'] = URL::to_asset($url);
			return static ::input('image', $name, null, $attributes);
		}
		public static function button($value = null, $attributes = array())
		{
			return '<button' . HTML::attributes($attributes) . '>' . HTML::entities($value) . '</button>';
		}
		protected static function id($name, $attributes)
		{
			if (array_key_exists('id', $attributes)) {
				return $attributes['id'];
			}
			if (in_array($name, static ::$labels)) {
				return $name;
			}
		}
		public static function __callStatic($method, $parameters)
		{
			if (isset(static ::$macros[$method])) {
				return call_user_func_array(static ::$macros[$method], $parameters);
			}
			throw new \Exception("Method [$method] does not exist.");
		}
	}
	/**
	 * laravel\hash.php
	 */
	class Hash
	{
		public static function make($value, $rounds = 8)
		{
			$work = str_pad($rounds, 2, '0', STR_PAD_LEFT);
			if (function_exists('openssl_random_pseudo_bytes')) {
				$salt = openssl_random_pseudo_bytes(16);
			} else {
				$salt = Str::random(40);
			}
			$salt = substr(strtr(base64_encode($salt), '+', '.'), 0, 22);
			return crypt($value, '$2a$' . $work . '$' . $salt);
		}
		public static function check($value, $hash)
		{
			return crypt($value, $hash) === $hash;
		}
	}
	/**
	 * laravel\html.php
	 */
	class HTML
	{
		public static $macros = array();
		public static $encoding = null;
		public static function macro($name, $macro)
		{
			static ::$macros[$name] = $macro;
		}
		public static function entities($value)
		{
			return htmlentities($value, ENT_QUOTES, static ::encoding(), false);
		}
		public static function decode($value)
		{
			return html_entity_decode($value, ENT_QUOTES, static ::encoding());
		}
		public static function specialchars($value)
		{
			return htmlspecialchars($value, ENT_QUOTES, static ::encoding(), false);
		}
		public static function script($url, $attributes = array())
		{
			$url = URL::to_asset($url);
			return '<script src="' . $url . '"' . static ::attributes($attributes) . '></script>' . PHP_EOL;
		}
		public static function style($url, $attributes = array())
		{
			$defaults = array('media' => 'all', 'type' => 'text/css', 'rel' => 'stylesheet');
			$attributes = $attributes + $defaults;
			$url = URL::to_asset($url);
			return '<link href="' . $url . '"' . static ::attributes($attributes) . '>' . PHP_EOL;
		}
		public static function span($value, $attributes = array())
		{
			return '<span' . static ::attributes($attributes) . '>' . static ::entities($value) . '</span>';
		}
		public static function link($url, $title = null, $attributes = array(), $https = null)
		{
			$url = URL::to($url, $https);
			if (is_null($title)) $title = $url;
			return '<a href="' . $url . '"' . static ::attributes($attributes) . '>' . static ::entities($title) . '</a>';
		}
		public static function link_to_secure($url, $title = null, $attributes = array())
		{
			return static ::link($url, $title, $attributes, true);
		}
		public static function link_to_asset($url, $title = null, $attributes = array(), $https = null)
		{
			$url = URL::to_asset($url, $https);
			if (is_null($title)) $title = $url;
			return '<a href="' . $url . '"' . static ::attributes($attributes) . '>' . static ::entities($title) . '</a>';
		}
		public static function link_to_secure_asset($url, $title = null, $attributes = array())
		{
			return static ::link_to_asset($url, $title, $attributes, true);
		}
		public static function link_to_route($name, $title = null, $parameters = array(), $attributes = array())
		{
			return static ::link(URL::to_route($name, $parameters), $title, $attributes);
		}
		public static function link_to_action($action, $title = null, $parameters = array(), $attributes = array())
		{
			return static ::link(URL::to_action($action, $parameters), $title, $attributes);
		}
		public static function link_to_language($language, $title = null, $attributes = array())
		{
			return static ::link(URL::to_language($language), $title, $attributes);
		}
		public static function mailto($email, $title = null, $attributes = array())
		{
			$email = static ::email($email);
			if (is_null($title)) $title = $email;
			$email = '&#109;&#097;&#105;&#108;&#116;&#111;&#058;' . $email;
			return '<a href="' . $email . '"' . static ::attributes($attributes) . '>' . static ::entities($title) . '</a>';
		}
		public static function email($email)
		{
			return str_replace('@', '&#64;', static ::obfuscate($email));
		}
		public static function image($url, $alt = '', $attributes = array())
		{
			$attributes['alt'] = $alt;
			return '<img src="' . URL::to_asset($url) . '"' . static ::attributes($attributes) . '>';
		}
		public static function ol($list, $attributes = array())
		{
			return static ::listing('ol', $list, $attributes);
		}
		public static function ul($list, $attributes = array())
		{
			return static ::listing('ul', $list, $attributes);
		}
		private static function listing($type, $list, $attributes = array())
		{
			$html = '';
			if (count($list) == 0) return $html;
			foreach ($list as $key => $value) {
				if (is_array($value)) {
					if (is_int($key)) {
						$html.= static ::listing($type, $value);
					} else {
						$html.= '<li>' . $key . static ::listing($type, $value) . '</li>';
					}
				} else {
					$html.= '<li>' . static ::entities($value) . '</li>';
				}
			}
			return '<' . $type . static ::attributes($attributes) . '>' . $html . '</' . $type . '>';
		}
		public static function dl($list, $attributes = array())
		{
			$html = '';
			if (count($list) == 0) return $html;
			foreach ($list as $term => $description) {
				$html.= '<dt>' . static ::entities($term) . '</dt>';
				$html.= '<dd>' . static ::entities($description) . '</dd>';
			}
			return '<dl' . static ::attributes($attributes) . '>' . $html . '</dl>';
		}
		public static function attributes($attributes)
		{
			$html = array();
			foreach ((array)$attributes as $key => $value) {
				if (is_numeric($key)) $key = $value;
				if (!is_null($value)) {
					$html[] = $key . '="' . static ::entities($value) . '"';
				}
			}
			return (count($html) > 0) ? ' ' . implode(' ', $html) : '';
		}
		protected static function obfuscate($value)
		{
			$safe = '';
			foreach (str_split($value) as $letter) {
				switch (rand(1, 3)) {
				case 1:
					$safe.= '&#' . ord($letter) . ';';
					break;

				case 2:
					$safe.= '&#x' . dechex(ord($letter)) . ';';
					break;

				case 3:
					$safe.= $letter;
				}
			}
			return $safe;
		}
		protected static function encoding()
		{
			return static ::$encoding ? : static ::$encoding = Config::get('application.encoding');
		}
		public static function __callStatic($method, $parameters)
		{
			if (isset(static ::$macros[$method])) {
				return call_user_func_array(static ::$macros[$method], $parameters);
			}
			throw new \Exception("Method [$method] does not exist.");
		}
	}
	/**
	 * laravel\input.php
	 */
	class Input
	{
		public static $json;
		const old_input = 'laravel_old_input';
		public static function all()
		{
			$input = array_merge(static ::get(), static ::query(), static ::file());
			unset($input[Request::spoofer]);
			return $input;
		}
		public static function has($key)
		{
			return trim((string)static ::get($key)) !== '';
		}
		public static function get($key = null, $default = null)
		{
			$input = Request::foundation()->request->all();
			if (is_null($key)) {
				return array_merge($input, static ::query());
			}
			$value = array_get($input, $key);
			if (is_null($value)) {
				return array_get(static ::query(), $key, $default);
			}
			return $value;
		}
		public static function query($key = null, $default = null)
		{
			return array_get(Request::foundation()->query->all(), $key, $default);
		}
		public static function json($as_array = false)
		{
			if (!is_null(static ::$json)) return static ::$json;
			return static ::$json = json_decode(Request::foundation()->getContent(), $as_array);
		}
		public static function only($keys)
		{
			return array_only(static ::get(), $keys);
		}
		public static function except($keys)
		{
			return array_except(static ::get(), $keys);
		}
		public static function had($key)
		{
			return trim((string)static ::old($key)) !== '';
		}
		public static function old($key = null, $default = null)
		{
			return array_get(Session::get(Input::old_input, array()), $key, $default);
		}
		public static function file($key = null, $default = null)
		{
			return array_get($_FILES, $key, $default);
		}
		public static function has_file($key)
		{
			return strlen(static ::file("{$key}.tmp_name", "")) > 0;
		}
		public static function upload($key, $directory, $name = null)
		{
			if (is_null(static ::file($key))) return false;
			return Request::foundation()->files->get($key)->move($directory, $name);
		}
		public static function flash($filter = null, $keys = array())
		{
			$flash = (!is_null($filter)) ? static ::$filter($keys) : static ::get();
			Session::flash(Input::old_input, $flash);
		}
		public static function flush()
		{
			Session::flash(Input::old_input, array());
		}
		public static function merge(array $input)
		{
			Request::foundation()->request->add($input);
		}
		public static function replace(array $input)
		{
			Request::foundation()->request->replace($input);
		}
		public static function clear()
		{
			Request::foundation()->request->replace(array());
		}
	}
	/**
	 * laravel\ioc.php
	 */
	class IoC
	{
		public static $registry = array();
		public static $singletons = array();
		public static function register($name, $resolver = null, $singleton = false)
		{
			if (is_null($resolver)) $resolver = $name;
			static ::$registry[$name] = compact('resolver', 'singleton');
		}
		public static function registered($name)
		{
			return array_key_exists($name, static ::$registry);
		}
		public static function singleton($name, $resolver = null)
		{
			static ::register($name, $resolver, true);
		}
		public static function instance($name, $instance)
		{
			static ::$singletons[$name] = $instance;
		}
		public static function resolve($type, $parameters = array())
		{
			if (isset(static ::$singletons[$type])) {
				return static ::$singletons[$type];
			}
			if (!isset(static ::$registry[$type])) {
				$concrete = $type;
			} else {
				$concrete = array_get(static ::$registry[$type], 'resolver', $type);
			}
			if ($concrete == $type or $concrete instanceof Closure) {
				$object = static ::build($concrete, $parameters);
			} else {
				$object = static ::resolve($concrete);
			}
			if (isset(static ::$registry[$type]['singleton']) && static ::$registry[$type]['singleton'] === true) {
				static ::$singletons[$type] = $object;
			}
			Event::fire('laravel.resolving', array($type, $object));
			return $object;
		}
		protected static function build($type, $parameters = array())
		{
			if ($type instanceof Closure) {
				return call_user_func_array($type, $parameters);
			}
			$reflector = new \ReflectionClass($type);
			if (!$reflector->isInstantiable()) {
				throw new \Exception("Resolution target [$type] is not instantiable.");
			}
			$constructor = $reflector->getConstructor();
			if (is_null($constructor)) {
				return new $type;
			}
			$dependencies = static ::dependencies($constructor->getParameters());
			return $reflector->newInstanceArgs($dependencies);
		}
		protected static function dependencies($parameters)
		{
			$dependencies = array();
			foreach ($parameters as $parameter) {
				$dependency = $parameter->getClass();
				if (is_null($dependency)) {
					throw new \Exception("Unresolvable dependency resolving [$parameter].");
				}
				$dependencies[] = static ::resolve($dependency->name);
			}
			return (array)$dependencies;
		}
	}
	/**
	 * laravel\lang.php
	 */
	class Lang
	{
		protected $key;
		protected $replacements;
		protected $language;
		protected static $lines = array();
		const loader = 'laravel.language.loader';
		protected function __construct($key, $replacements = array(), $language = null)
		{
			$this->key = $key;
			$this->language = $language;
			$this->replacements = (array)$replacements;
		}
		public static function line($key, $replacements = array(), $language = null)
		{
			if (is_null($language)) $language = Config::get('application.language');
			return new static ($key, $replacements, $language);
		}
		public static function has($key, $language = null)
		{
			return static ::line($key, array(), $language)->get() !== $key;
		}
		public function get($language = null, $default = null)
		{
			if (is_null($default)) $default = $this->key;
			if (is_null($language)) $language = $this->language;
			list($bundle, $file, $line) = $this->parse($this->key);
			if (!static ::load($bundle, $language, $file)) {
				return value($default);
			}
			$lines = static ::$lines[$bundle][$language][$file];
			$line = array_get($lines, $line, $default);
			if (is_string($line)) {
				foreach ($this->replacements as $key => $value) {
					$line = str_replace(':' . $key, $value, $line);
				}
			}
			return $line;
		}
		protected function parse($key)
		{
			$bundle = Bundle::name($key);
			$segments = explode('.', Bundle::element($key));
			if (count($segments) >= 2) {
				$line = implode('.', array_slice($segments, 1));
				return array($bundle, $segments[0], $line);
			} else {
				return array($bundle, $segments[0], null);
			}
		}
		public static function load($bundle, $language, $file)
		{
			if (isset(static ::$lines[$bundle][$language][$file])) {
				return true;
			}
			$lines = Event::first(static ::loader, func_get_args());
			static ::$lines[$bundle][$language][$file] = $lines;
			return count($lines) > 0;
		}
		public static function file($bundle, $language, $file)
		{
			$lines = array();
			$path = static ::path($bundle, $language, $file);
			if (file_exists($path)) {
				$lines = require $path;
			}
			return $lines;
		}
		protected static function path($bundle, $language, $file)
		{
			return Bundle::path($bundle) . "language/{$language}/{$file}" . EXT;
		}
		public function __toString()
		{
			return (string)$this->get();
		}
	}
	/**
	 * laravel\laravel.php
	 */
	set_exception_handler(function ($e)
	{
		#require_once path('sys') . 'error' . EXT;
		Error::exception($e);
	});
	set_error_handler(function ($code, $error, $file, $line)
	{
		#require_once path('sys') . 'error' . EXT;
		Error::native($code, $error, $file, $line);
	});
	register_shutdown_function(function ()
	{
		#require_once path('sys') . 'error' . EXT;
		Error::shutdown();
	});
	error_reporting(-1);
	Bundle::start(DEFAULT_BUNDLE);
	foreach (Bundle::$bundles as $bundle => $config) {
		if ($config['auto']) Bundle::start($bundle);
	}
	Router::register('*', '(:all)', function ()
	{
		return Event::first('404');
	});
	$uri = URI::current();
	$languages = Config::get('application.languages', array());
	$languages[] = Config::get('application.language');
	foreach ($languages as $language) {
		if (preg_match("#^{$language}(?:$|/)#i", $uri)) {
			Config::set('application.language', $language);
			$uri = trim(substr($uri, strlen($language)), '/');
			break;
		}
	}
	if ($uri == '') $uri = '/';
	URI::$uri = $uri;
	Request::$route = Router::route(Request::method(), $uri);
	$response = Request::$route->call();
	$response->render();
	if (Config::get('session.driver') !== '') {
		Session::save();
	}
	$response->send();
	Event::fire('laravel.done', array($response));
	$response->foundation->finish();
	/**
	 * laravel\log.php
	 */
	class Log
	{
		public static function exception($e)
		{
			static ::write('error', static ::exception_line($e));
		}
		protected static function exception_line($e)
		{
			return $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
		}
		public static function write($type, $message, $pretty_print = false)
		{
			$message = ($pretty_print) ? print_r($message, true) : $message;
			if (Event::listeners('laravel.log')) {
				Event::fire('laravel.log', array($type, $message));
			}
			$message = static ::format($type, $message);
			File::append(path('storage') . 'logs/' . date('Y-m-d') . '.log', $message);
		}
		protected static function format($type, $message)
		{
			return date('Y-m-d H:i:s') . ' ' . Str::upper($type) . " - {$message}" . PHP_EOL;
		}
		public static function __callStatic($method, $parameters)
		{
			$parameters[1] = (empty($parameters[1])) ? false : $parameters[1];
			static ::write($method, $parameters[0], $parameters[1]);
		}
	}
	/**
	 * laravel\memcached.php
	 */
	class Memcached
	{
		protected static $connection;
		public static function connection()
		{
			if (is_null(static ::$connection)) {
				static ::$connection = static ::connect(Config::get('cache.memcached'));
			}
			return static ::$connection;
		}
		protected static function connect($servers)
		{
			$memcache = new \Memcached;
			foreach ($servers as $server) {
				$memcache->addServer($server['host'], $server['port'], $server['weight']);
			}
			if ($memcache->getVersion() === false) {
				throw new \Exception('Could not establish memcached connection.');
			}
			return $memcache;
		}
		public static function __callStatic($method, $parameters)
		{
			return call_user_func_array(array(static ::connection(), $method), $parameters);
		}
	}
	/**
	 * laravel\messages.php
	 */
	class Messages
	{
		public $messages;
		public $format = ':message';
		public function __construct($messages = array())
		{
			$this->messages = (array)$messages;
		}
		public function add($key, $message)
		{
			if ($this->unique($key, $message)) $this->messages[$key][] = $message;
		}
		protected function unique($key, $message)
		{
			return !isset($this->messages[$key]) or !in_array($message, $this->messages[$key]);
		}
		public function has($key = null)
		{
			return $this->first($key) !== '';
		}
		public function format($format = ':message')
		{
			$this->format = $format;
		}
		public function first($key = null, $format = null)
		{
			$format = ($format === null) ? $this->format : $format;
			$messages = is_null($key) ? $this->all($format) : $this->get($key, $format);
			return (count($messages) > 0) ? $messages[0] : '';
		}
		public function get($key, $format = null)
		{
			$format = ($format === null) ? $this->format : $format;
			if (array_key_exists($key, $this->messages)) {
				return $this->transform($this->messages[$key], $format);
			}
			return array();
		}
		public function all($format = null)
		{
			$format = ($format === null) ? $this->format : $format;
			$all = array();
			foreach ($this->messages as $messages) {
				$all = array_merge($all, $this->transform($messages, $format));
			}
			return $all;
		}
		protected function transform($messages, $format)
		{
			$messages = (array)$messages;
			foreach ($messages as $key => & $message) {
				$message = str_replace(':message', $message, $format);
			}
			return $messages;
		}
	}
	/**
	 * laravel\paginator.php
	 */
	class Paginator
	{
		public $results;
		public $page;
		public $last;
		public $total;
		public $per_page;
		protected $appends;
		protected $appendage;
		protected $language;
		protected $dots = '<li class="dots disabled"><a href="#">...</a></li>';
		protected function __construct($results, $page, $total, $per_page, $last)
		{
			$this->page = $page;
			$this->last = $last;
			$this->total = $total;
			$this->results = $results;
			$this->per_page = $per_page;
		}
		public static function make($results, $total, $per_page)
		{
			$page = static ::page($total, $per_page);
			$last = ceil($total / $per_page);
			return new static ($results, $page, $total, $per_page, $last);
		}
		public static function page($total, $per_page)
		{
			$page = Input::get('page', 1);
			if (is_numeric($page) and $page > $last = ceil($total / $per_page)) {
				return ($last > 0) ? $last : 1;
			}
			return (static ::valid($page)) ? $page : 1;
		}
		protected static function valid($page)
		{
			return $page >= 1 and filter_var($page, FILTER_VALIDATE_INT) !== false;
		}
		public function links($adjacent = 3)
		{
			if ($this->last <= 1) return '';
			if ($this->last < 7 + ($adjacent * 2)) {
				$links = $this->range(1, $this->last);
			} else {
				$links = $this->slider($adjacent);
			}
			$content = '<ul>' . $this->previous() . $links . $this->next() . '</ul>';
			return '<div class="pagination">' . $content . '</div>';
		}
		public function slider($adjacent = 3)
		{
			$window = $adjacent * 2;
			if ($this->page <= $window) {
				return $this->range(1, $window + 2) . ' ' . $this->ending();
			} elseif ($this->page >= $this->last - $window) {
				return $this->beginning() . ' ' . $this->range($this->last - $window - 2, $this->last);
			}
			$content = $this->range($this->page - $adjacent, $this->page + $adjacent);
			return $this->beginning() . ' ' . $content . ' ' . $this->ending();
		}
		public function previous($text = null)
		{
			$disabled = function ($page)
			{
				return $page <= 1;
			};
			return $this->element(__FUNCTION__, $this->page - 1, $text, $disabled);
		}
		public function next($text = null)
		{
			$disabled = function ($page, $last)
			{
				return $page >= $last;
			};
			return $this->element(__FUNCTION__, $this->page + 1, $text, $disabled);
		}
		protected function element($element, $page, $text, $disabled)
		{
			$class = "{$element}_page";
			if (is_null($text)) {
				$text = Lang::line("pagination.{$element}")->get($this->language);
			}
			if ($disabled($this->page, $this->last)) {
				return '<li' . HTML::attributes(array('class' => "{$class} disabled")) . '><a href="#">' . $text . '</a></li>';
			} else {
				return $this->link($page, $text, $class);
			}
		}
		protected function beginning()
		{
			return $this->range(1, 2) . ' ' . $this->dots;
		}
		protected function ending()
		{
			return $this->dots . ' ' . $this->range($this->last - 1, $this->last);
		}
		protected function range($start, $end)
		{
			$pages = array();
			for ($page = $start; $page <= $end; $page++) {
				if ($this->page == $page) {
					$pages[] = '<li class="active"><a href="#">' . $page . '</a></li>';
				} else {
					$pages[] = $this->link($page, $page, null);
				}
			}
			return implode(' ', $pages);
		}
		protected function link($page, $text, $class)
		{
			$query = '?page=' . $page . $this->appendage($this->appends);
			return '<li' . HTML::attributes(array('class' => $class)) . '>' . HTML::link(URI::current() . $query, $text, array(), Request::secure()) . '</li>';
		}
		protected function appendage($appends)
		{
			if (!is_null($this->appendage)) return $this->appendage;
			if (count($appends) <= 0) {
				return $this->appendage = '';
			}
			return $this->appendage = '&' . http_build_query($appends);
		}
		public function appends($values)
		{
			$this->appends = $values;
			return $this;
		}
		public function speaks($language)
		{
			$this->language = $language;
			return $this;
		}
	}
	/**
	 * laravel\pluralizer.php
	 */
	class Pluralizer
	{
		protected $config;
		protected $plural = array();
		protected $singular = array();
		public function __construct($config)
		{
			$this->config = $config;
		}
		public function singular($value)
		{
			if (isset($this->singular[$value])) {
				return $this->singular[$value];
			}
			$irregular = $this->config['irregular'];
			$result = $this->auto($value, $this->config['singular'], $irregular);
			return $this->singular[$value] = $result ? : $value;
		}
		public function plural($value, $count = 2)
		{
			if ($count == 1) return $value;
			if (isset($this->plural[$value])) {
				return $this->plural[$value];
			}
			$irregular = array_flip($this->config['irregular']);
			$result = $this->auto($value, $this->config['plural'], $irregular);
			return $this->plural[$value] = $result;
		}
		protected function auto($value, $source, $irregular)
		{
			if (in_array(Str::lower($value), $this->config['uncountable'])) {
				return $value;
			}
			foreach ($irregular as $irregular => $pattern) {
				if (preg_match($pattern = '/' . $pattern . '$/i', $value)) {
					return preg_replace($pattern, $irregular, $value);
				}
			}
			foreach ($source as $pattern => $inflected) {
				if (preg_match($pattern, $value)) {
					return preg_replace($pattern, $inflected, $value);
				}
			}
		}
	}
	/**
	 * laravel\redis.php
	 */
	class Redis
	{
		protected $host;
		protected $port;
		protected $database;
		protected $connection;
		protected static $databases = array();
		public function __construct($host, $port, $database = 0)
		{
			$this->host = $host;
			$this->port = $port;
			$this->database = $database;
		}
		public static function db($name = 'default')
		{
			if (!isset(static ::$databases[$name])) {
				if (is_null($config = Config::get("database.redis.{$name}"))) {
					throw new \Exception("Redis database [$name] is not defined.");
				}
				extract($config);
				static ::$databases[$name] = new static ($host, $port, $database);
			}
			return static ::$databases[$name];
		}
		public function run($method, $parameters)
		{
			fwrite($this->connect(), $this->command($method, (array)$parameters));
			$response = trim(fgets($this->connection, 512));
			return $this->parse($response);
		}
		protected function parse($response)
		{
			switch (substr($response, 0, 1)) {
			case '-':
				throw new \Exception('Redis error: ' . substr(trim($response), 4));
			case '+':
			case ':':
				return $this->inline($response);
			case '$':
				return $this->bulk($response);
			case '*':
				return $this->multibulk($response);
			default:
				throw new \Exception("Unknown Redis response: " . substr($response, 0, 1));
			}
		}
		protected function connect()
		{
			if (!is_null($this->connection)) return $this->connection;
			$this->connection = @fsockopen($this->host, $this->port, $error, $message);
			if ($this->connection === false) {
				throw new \Exception("Error making Redis connection: {$error} - {$message}");
			}
			$this->select($this->database);
			return $this->connection;
		}
		protected function command($method, $parameters)
		{
			$command = '*' . (count($parameters) + 1) . CRLF;
			$command.= '$' . strlen($method) . CRLF;
			$command.= strtoupper($method) . CRLF;
			foreach ($parameters as $parameter) {
				$command.= '$' . strlen($parameter) . CRLF . $parameter . CRLF;
			}
			return $command;
		}
		protected function inline($response)
		{
			return substr(trim($response), 1);
		}
		protected function bulk($head)
		{
			if ($head == '$-1') return;
			list($read, $response, $size) = array(0, '', substr($head, 1));
			if ($size > 0) {
				do {
					$block = (($remaining = $size - $read) < 1024) ? $remaining : 1024;
					$response.= fread($this->connection, $block);
					$read+= $block;
				} while ($read < $size);
			}
			fread($this->connection, 2);
			return $response;
		}
		protected function multibulk($head)
		{
			if (($count = substr($head, 1)) == '-1') return;
			$response = array();
			for ($i = 0; $i < $count; $i++) {
				$response[] = $this->parse(trim(fgets($this->connection, 512)));
			}
			return $response;
		}
		public function __call($method, $parameters)
		{
			return $this->run($method, $parameters);
		}
		public static function __callStatic($method, $parameters)
		{
			return static ::db()->run($method, $parameters);
		}
		public function __destruct()
		{
			if ($this->connection) {
				fclose($this->connection);
			}
		}
	}
	/**
	 * laravel\request.php
	 */
	class Request
	{
		public static $route;
		public static $foundation;
		const spoofer = '_method';
		public static function uri()
		{
			return URI::current();
		}
		public static function method()
		{
			$method = static ::foundation()->getMethod();
			return ($method == 'HEAD') ? 'GET' : $method;
		}
		public static function header($key, $default = null)
		{
			return array_get(static ::foundation()->headers->all(), $key, $default);
		}
		public static function headers()
		{
			return static ::foundation()->headers->all();
		}
		public static function server($key = null, $default = null)
		{
			return array_get(static ::foundation()->server->all(), strtoupper($key), $default);
		}
		public static function spoofed()
		{
			return !is_null(static ::foundation()->get(Request::spoofer));
		}
		public static function ip($default = '0.0.0.0')
		{
			$client_ip = static ::foundation()->getClientIp();
			return $client_ip === NULL ? $default : $client_ip;
		}
		public static function accept()
		{
			return static ::foundation()->getAcceptableContentTypes();
		}
		public static function accepts($type)
		{
			return in_array($type, static ::accept());
		}
		public static function languages()
		{
			return static ::foundation()->getLanguages();
		}
		public static function secure()
		{
			return static ::foundation()->isSecure() and Config::get('application.ssl');
		}
		public static function forged()
		{
			return Input::get(Session::csrf_token) !== Session::token();
		}
		public static function ajax()
		{
			return static ::foundation()->isXmlHttpRequest();
		}
		public static function referrer()
		{
			return static ::foundation()->headers->get('referer');
		}
		public static function time()
		{
			return (int)LARAVEL_START;
		}
		public static function cli()
		{
			return defined('STDIN') || (substr(PHP_SAPI, 0, 3) == 'cgi' && getenv('TERM'));
		}
		public static function env()
		{
			return static ::foundation()->server->get('LARAVEL_ENV');
		}
		public static function set_env($env)
		{
			static ::foundation()->server->set('LARAVEL_ENV', $env);
		}
		public static function is_env($env)
		{
			return static ::env() === $env;
		}
		public static function detect_env(array $environments, $uri)
		{
			foreach ($environments as $environment => $patterns) {
				foreach ($patterns as $pattern) {
					if (Str::is($pattern, $uri) or $pattern == gethostname()) {
						return $environment;
					}
				}
			}
		}
		public static function route()
		{
			return static ::$route;
		}
		public static function foundation()
		{
			return static ::$foundation;
		}
		public static function __callStatic($method, $parameters)
		{
			return call_user_func_array(array(static ::foundation(), $method), $parameters);
		}
	}
	/**
	 * laravel\section.php
	 */
	class Section
	{
		public static $sections = array();
		public static $last = array();
		public static function start($section, $content = '')
		{
			if ($content === '') {
				ob_start() and static ::$last[] = $section;
			} else {
				static ::extend($section, $content);
			}
		}
		public static function inject($section, $content)
		{
			static ::start($section, $content);
		}
		public static function yield_section()
		{
			return static ::yield(static ::stop());
		}
		public static function stop()
		{
			static ::extend($last = array_pop(static ::$last), ob_get_clean());
			return $last;
		}
		protected static function extend($section, $content)
		{
			if (isset(static ::$sections[$section])) {
				static ::$sections[$section] = str_replace('@parent', $content, static ::$sections[$section]);
			} else {
				static ::$sections[$section] = $content;
			}
		}
		public static function append($section, $content)
		{
			if (isset(static ::$sections[$section])) {
				static ::$sections[$section].= $content;
			} else {
				static ::$sections[$section] = $content;
			}
		}
		public static function yield($section)
		{
			return (isset(static ::$sections[$section])) ? static ::$sections[$section] : '';
		}
	}
	/**
	 * laravel\session.php
	 */
	class Session
	{
		public static $instance;
		public static $registrar = array();
		const csrf_token = 'csrf_token';
		public static function load()
		{
			static ::start(Config::get('session.driver'));
			static ::$instance->load(Cookie::get(Config::get('session.cookie')));
		}
		public static function start($driver)
		{
			static ::$instance = new Session\Payload(static ::factory($driver));
		}
		public static function factory($driver)
		{
			if (isset(static ::$registrar[$driver])) {
				$resolver = static ::$registrar[$driver];
				return $resolver();
			}
			switch ($driver) {
			case 'apc':
				return new Session\Drivers\APC(Cache::driver('apc'));
			case 'cookie':
				return new Session\Drivers\Cookie;
			case 'database':
				return new Session\Drivers\Database(Database::connection());
			case 'file':
				return new Session\Drivers\File(path('storage') . 'sessions' . DS);
			case 'memcached':
				return new Session\Drivers\Memcached(Cache::driver('memcached'));
			case 'memory':
				return new Session\Drivers\Memory;
			case 'redis':
				return new Session\Drivers\Redis(Cache::driver('redis'));
			default:
				throw new \Exception("Session driver [$driver] is not supported.");
			}
		}
		public static function instance()
		{
			if (static ::started()) return static ::$instance;
			throw new \Exception("A driver must be set before using the session.");
		}
		public static function started()
		{
			return !is_null(static ::$instance);
		}
		public static function extend($driver, Closure $resolver)
		{
			static ::$registrar[$driver] = $resolver;
		}
		public static function __callStatic($method, $parameters)
		{
			return call_user_func_array(array(static ::instance(), $method), $parameters);
		}
	}
	/**
	 * laravel\str.php
	 */
	class Str
	{
		public static $pluralizer;
		public static $encoding = null;
		protected static function encoding()
		{
			return static ::$encoding ? : static ::$encoding = Config::get('application.encoding');
		}
		public static function length($value)
		{
			return (MB_STRING) ? mb_strlen($value, static ::encoding()) : strlen($value);
		}
		public static function lower($value)
		{
			return (MB_STRING) ? mb_strtolower($value, static ::encoding()) : strtolower($value);
		}
		public static function upper($value)
		{
			return (MB_STRING) ? mb_strtoupper($value, static ::encoding()) : strtoupper($value);
		}
		public static function title($value)
		{
			if (MB_STRING) {
				return mb_convert_case($value, MB_CASE_TITLE, static ::encoding());
			}
			return ucwords(strtolower($value));
		}
		public static function limit($value, $limit = 100, $end = '...')
		{
			if (static ::length($value) <= $limit) return $value;
			if (MB_STRING) {
				return mb_substr($value, 0, $limit, static ::encoding()) . $end;
			}
			return substr($value, 0, $limit) . $end;
		}
		public static function limit_exact($value, $limit = 100, $end = '...')
		{
			if (static ::length($value) <= $limit) return $value;
			$limit-= static ::length($end);
			return static ::limit($value, $limit, $end);
		}
		public static function words($value, $words = 100, $end = '...')
		{
			if (trim($value) == '') return '';
			preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $value, $matches);
			if (static ::length($value) == static ::length($matches[0])) {
				$end = '';
			}
			return rtrim($matches[0]) . $end;
		}
		public static function singular($value)
		{
			return static ::pluralizer()->singular($value);
		}
		public static function plural($value, $count = 2)
		{
			return static ::pluralizer()->plural($value, $count);
		}
		protected static function pluralizer()
		{
			$config = Config::get('strings');
			return static ::$pluralizer ? : static ::$pluralizer = new Pluralizer($config);
		}
		public static function slug($title, $separator = '-')
		{
			$title = static ::ascii($title);
			$title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', static ::lower($title));
			$title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);
			return trim($title, $separator);
		}
		public static function ascii($value)
		{
			$foreign = Config::get('strings.ascii');
			$value = preg_replace(array_keys($foreign), array_values($foreign), $value);
			return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $value);
		}
		public static function classify($value)
		{
			$search = array('_', '-', '.', '/');
			return str_replace(' ', '_', static ::title(str_replace($search, ' ', $value)));
		}
		public static function segments($value)
		{
			return array_diff(explode('/', trim($value, '/')), array(''));
		}
		public static function random($length, $type = 'alnum')
		{
			return substr(str_shuffle(str_repeat(static ::pool($type), 5)), 0, $length);
		}
		public static function is($pattern, $value)
		{
			if ($pattern !== '/') {
				$pattern = str_replace('*', '(.*)', $pattern) . '\z';
			} else {
				$pattern = '^/$';
			}
			return preg_match('#' . $pattern . '#', $value);
		}
		protected static function pool($type)
		{
			switch ($type) {
			case 'alpha':
				return 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			case 'alnum':
				return '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			default:
				throw new \Exception("Invalid random string type [$type].");
			}
		}
	}
	/**
	 * laravel\uri.php
	 */
	class URI
	{
		public static $uri;
		public static $segments = array();
		public static function full()
		{
			return Request::getUri();
		}
		public static function current()
		{
			if (!is_null(static ::$uri)) return static ::$uri;
			$uri = static ::format(Request::getPathInfo());
			static ::segments($uri);
			return static ::$uri = $uri;
		}
		protected static function format($uri)
		{
			return trim($uri, '/') ? : '/';
		}
		public static function is($pattern)
		{
			return Str::is($pattern, static ::current());
		}
		public static function segment($index, $default = null)
		{
			static ::current();
			return array_get(static ::$segments, $index - 1, $default);
		}
		protected static function segments($uri)
		{
			$segments = explode('/', trim($uri, '/'));
			static ::$segments = array_diff($segments, array(''));
		}
	}
	/**
	 * laravel\url.php
	 */
	class URL
	{
		public static $base;
		public static function full()
		{
			return static ::to(URI::full());
		}
		public static function current()
		{
			return static ::to(URI::current(), null, false, false);
		}
		public static function home($https = null)
		{
			$route = Router::find('home');
			if (!is_null($route)) {
				return static ::to_route('home');
			}
			return static ::to('/', $https);
		}
		public static function base()
		{
			if (isset(static ::$base)) return static ::$base;
			$base = 'http://localhost';
			if (($url = Config::get('application.url')) !== '') {
				$base = $url;
			} else {
				$base = Request::foundation()->getRootUrl();
			}
			return static ::$base = $base;
		}
		public static function to($url = '', $https = null, $asset = false, $locale = true)
		{
			if (static ::valid($url) or starts_with($url, '#')) {
				return $url;
			}
			if (is_null($https)) $https = Request::secure();
			$root = static ::base();
			if (!$asset) {
				$root.= '/' . Config::get('application.index');
			}
			$languages = Config::get('application.languages');
			if (!$asset and $locale and count($languages) > 0) {
				if (in_array($default = Config::get('application.language'), $languages)) {
					$root = rtrim($root, '/') . '/' . $default;
				}
			}
			if ($https and Config::get('application.ssl')) {
				$root = preg_replace('~http://~', 'https://', $root, 1);
			} else {
				$root = preg_replace('~https://~', 'http://', $root, 1);
			}
			return rtrim($root, '/') . '/' . ltrim($url, '/');
		}
		public static function to_secure($url = '')
		{
			return static ::to($url, true);
		}
		public static function to_action($action, $parameters = array())
		{
			$route = Router::uses($action);
			if (!is_null($route)) {
				return static ::explicit($route, $action, $parameters);
			} else {
				return static ::convention($action, $parameters);
			}
		}
		protected static function explicit($route, $action, $parameters)
		{
			$https = array_get(current($route), 'https', null);
			return static ::to(static ::transpose(key($route), $parameters), $https);
		}
		protected static function convention($action, $parameters)
		{
			list($bundle, $action) = Bundle::parse($action);
			$bundle = Bundle::get($bundle);
			$root = $bundle['handles'] ? : '';
			$parameters = implode('/', $parameters);
			$uri = $root . '/' . str_replace(array('.', '@'), '/', $action);
			$uri = static ::to(str_finish($uri, '/') . $parameters);
			return trim($uri, '/');
		}
		public static function to_asset($url, $https = null)
		{
			if (static ::valid($url) or static ::valid('http:' . $url)) return $url;
			if ($root = Config::get('application.asset_url', false)) {
				return rtrim($root, '/') . '/' . ltrim($url, '/');
			}
			$url = static ::to($url, $https, true);
			if (($index = Config::get('application.index')) !== '') {
				$url = str_replace($index . '/', '', $url);
			}
			return $url;
		}
		public static function to_route($name, $parameters = array())
		{
			if (is_null($route = Routing\Router::find($name))) {
				throw new \Exception("Error creating URL for undefined route [$name].");
			}
			$https = array_get(current($route), 'https', null);
			$uri = trim(static ::transpose(key($route), $parameters), '/');
			return static ::to($uri, $https);
		}
		public static function to_language($language, $reset = false)
		{
			$url = $reset ? URL::home() : URL::to(URI::current());
			if (!in_array($language, Config::get('application.languages'))) {
				return $url;
			}
			$from = '/' . Config::get('application.language') . '/';
			$to = '/' . $language . '/';
			return str_replace($from, $to, $url);
		}
		public static function transpose($uri, $parameters)
		{
			foreach ((array)$parameters as $parameter) {
				if (!is_null($parameter)) {
					$uri = preg_replace('/\(.+?\)/', $parameter, $uri, 1);
				}
			}
			$uri = preg_replace('/\(.+?\)/', '', $uri);
			return trim($uri, '/');
		}
		public static function valid($url)
		{
			return filter_var($url, FILTER_VALIDATE_URL) !== false;
		}
	}
	/**
	 * laravel\validator.php
	 */
	class Validator
	{
		public $attributes;
		public $errors;
		protected $rules = array();
		protected $messages = array();
		protected $db;
		protected $bundle = DEFAULT_BUNDLE;
		protected $language;
		protected $size_rules = array('size', 'between', 'min', 'max');
		protected $numeric_rules = array('numeric', 'integer');
		protected static $validators = array();
		public function __construct($attributes, $rules, $messages = array())
		{
			foreach ($rules as $key => & $rule) {
				$rule = (is_string($rule)) ? explode('|', $rule) : $rule;
			}
			$this->rules = $rules;
			$this->messages = $messages;
			$this->attributes = (is_object($attributes)) ? get_object_vars($attributes) : $attributes;
		}
		public static function make($attributes, $rules, $messages = array())
		{
			return new static ($attributes, $rules, $messages);
		}
		public static function register($name, $validator)
		{
			static ::$validators[$name] = $validator;
		}
		public function passes()
		{
			return $this->valid();
		}
		public function fails()
		{
			return $this->invalid();
		}
		public function invalid()
		{
			return !$this->valid();
		}
		public function valid()
		{
			$this->errors = new Messages;
			foreach ($this->rules as $attribute => $rules) {
				foreach ($rules as $rule) $this->check($attribute, $rule);
			}
			return count($this->errors->messages) == 0;
		}
		protected function check($attribute, $rule)
		{
			list($rule, $parameters) = $this->parse($rule);
			$value = array_get($this->attributes, $attribute);
			$validatable = $this->validatable($rule, $attribute, $value);
			if ($validatable and !$this->{'validate_' . $rule}($attribute, $value, $parameters, $this)) {
				$this->error($attribute, $rule, $parameters);
			}
		}
		protected function validatable($rule, $attribute, $value)
		{
			return $this->validate_required($attribute, $value) or $this->implicit($rule);
		}
		protected function implicit($rule)
		{
			return $rule == 'required' or $rule == 'accepted' or $rule == 'required_with';
		}
		protected function error($attribute, $rule, $parameters)
		{
			$message = $this->replace($this->message($attribute, $rule), $attribute, $rule, $parameters);
			$this->errors->add($attribute, $message);
		}
		protected function validate_required($attribute, $value)
		{
			if (is_null($value)) {
				return false;
			} elseif (is_string($value) and trim($value) === '') {
				return false;
			} elseif (!is_null(Input::file($attribute)) and is_array($value) and $value['tmp_name'] == '') {
				return false;
			}
			return true;
		}
		protected function validate_required_with($attribute, $value, $parameters)
		{
			$other = $parameters[0];
			$other_value = array_get($this->attributes, $other);
			if ($this->validate_required($other, $other_value)) {
				return $this->validate_required($attribute, $value);
			}
			return true;
		}
		protected function validate_confirmed($attribute, $value)
		{
			return $this->validate_same($attribute, $value, array($attribute . '_confirmation'));
		}
		protected function validate_accepted($attribute, $value)
		{
			return $this->validate_required($attribute, $value) and ($value == 'yes' or $value == '1' or $value == 'on');
		}
		protected function validate_same($attribute, $value, $parameters)
		{
			$other = $parameters[0];
			return array_key_exists($other, $this->attributes) and $value == $this->attributes[$other];
		}
		protected function validate_different($attribute, $value, $parameters)
		{
			$other = $parameters[0];
			return array_key_exists($other, $this->attributes) and $value != $this->attributes[$other];
		}
		protected function validate_numeric($attribute, $value)
		{
			return is_numeric($value);
		}
		protected function validate_integer($attribute, $value)
		{
			return filter_var($value, FILTER_VALIDATE_INT) !== false;
		}
		protected function validate_size($attribute, $value, $parameters)
		{
			return $this->size($attribute, $value) == $parameters[0];
		}
		protected function validate_between($attribute, $value, $parameters)
		{
			$size = $this->size($attribute, $value);
			return $size >= $parameters[0] and $size <= $parameters[1];
		}
		protected function validate_min($attribute, $value, $parameters)
		{
			return $this->size($attribute, $value) >= $parameters[0];
		}
		protected function validate_max($attribute, $value, $parameters)
		{
			return $this->size($attribute, $value) <= $parameters[0];
		}
		protected function size($attribute, $value)
		{
			if (is_numeric($value) and $this->has_rule($attribute, $this->numeric_rules)) {
				return $this->attributes[$attribute];
			} elseif (array_key_exists($attribute, Input::file())) {
				return $value['size'] / 1024;
			} else {
				return Str::length(trim($value));
			}
		}
		protected function validate_in($attribute, $value, $parameters)
		{
			return in_array($value, $parameters);
		}
		protected function validate_not_in($attribute, $value, $parameters)
		{
			return !in_array($value, $parameters);
		}
		protected function validate_unique($attribute, $value, $parameters)
		{
			if (isset($parameters[1])) {
				$attribute = $parameters[1];
			}
			$query = $this->db()->table($parameters[0])->where($attribute, '=', $value);
			if (isset($parameters[2])) {
				$id = (isset($parameters[3])) ? $parameters[3] : 'id';
				$query->where($id, '<>', $parameters[2]);
			}
			return $query->count() == 0;
		}
		protected function validate_exists($attribute, $value, $parameters)
		{
			if (isset($parameters[1])) $attribute = $parameters[1];
			$count = (is_array($value)) ? count($value) : 1;
			$query = $this->db()->table($parameters[0]);
			if (is_array($value)) {
				$query = $query->where_in($attribute, $value);
			} else {
				$query = $query->where($attribute, '=', $value);
			}
			return $query->count() >= $count;
		}
		protected function validate_ip($attribute, $value)
		{
			return filter_var($value, FILTER_VALIDATE_IP) !== false;
		}
		protected function validate_email($attribute, $value)
		{
			return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
		}
		protected function validate_url($attribute, $value)
		{
			return filter_var($value, FILTER_VALIDATE_URL) !== false;
		}
		protected function validate_active_url($attribute, $value)
		{
			$url = str_replace(array('http://', 'https://', 'ftp://'), '', Str::lower($value));
			return (trim($url) !== '') ? checkdnsrr($url) : false;
		}
		protected function validate_image($attribute, $value)
		{
			return $this->validate_mimes($attribute, $value, array('jpg', 'png', 'gif', 'bmp'));
		}
		protected function validate_alpha($attribute, $value)
		{
			return preg_match('/^([a-z])+$/i', $value);
		}
		protected function validate_alpha_num($attribute, $value)
		{
			return preg_match('/^([a-z0-9])+$/i', $value);
		}
		protected function validate_alpha_dash($attribute, $value)
		{
			return preg_match('/^([-a-z0-9_-])+$/i', $value);
		}
		protected function validate_match($attribute, $value, $parameters)
		{
			return preg_match($parameters[0], $value);
		}
		protected function validate_mimes($attribute, $value, $parameters)
		{
			if (!is_array($value) or array_get($value, 'tmp_name', '') == '') return true;
			foreach ($parameters as $extension) {
				if (File::is($extension, $value['tmp_name'])) {
					return true;
				}
			}
			return false;
		}
		protected function validate_array($attribute, $value)
		{
			return is_array($value);
		}
		protected function validate_count($attribute, $value, $parameters)
		{
			return (is_array($value) && count($value) == $parameters[0]);
		}
		protected function validate_countmin($attribute, $value, $parameters)
		{
			return (is_array($value) && count($value) >= $parameters[0]);
		}
		protected function validate_countmax($attribute, $value, $parameters)
		{
			return (is_array($value) && count($value) <= $parameters[0]);
		}
		protected function validate_countbetween($attribute, $value, $parameters)
		{
			return (is_array($value) && count($value) >= $parameters[0] && count($value) <= $parameters[1]);
		}
		protected function validate_before($attribute, $value, $parameters)
		{
			return (strtotime($value) < strtotime($parameters[0]));
		}
		protected function validate_after($attribute, $value, $parameters)
		{
			return (strtotime($value) > strtotime($parameters[0]));
		}
		protected function validate_date_format($attribute, $value, $parameters)
		{
			return date_create_from_format($parameters[0], $value) !== false;
		}
		protected function message($attribute, $rule)
		{
			$bundle = Bundle::prefix($this->bundle);
			$custom = $attribute . '_' . $rule;
			if (array_key_exists($custom, $this->messages)) {
				return $this->messages[$custom];
			} elseif (Lang::has($custom = "{$bundle}validation.custom.{$custom}", $this->language)) {
				return Lang::line($custom)->get($this->language);
			} elseif (array_key_exists($rule, $this->messages)) {
				return $this->messages[$rule];
			} elseif (in_array($rule, $this->size_rules)) {
				return $this->size_message($bundle, $attribute, $rule);
			} else {
				$line = "{$bundle}validation.{$rule}";
				return Lang::line($line)->get($this->language);
			}
		}
		protected function size_message($bundle, $attribute, $rule)
		{
			if ($this->has_rule($attribute, $this->numeric_rules)) {
				$line = 'numeric';
			} elseif (array_key_exists($attribute, Input::file())) {
				$line = 'file';
			} else {
				$line = 'string';
			}
			return Lang::line("{$bundle}validation.{$rule}.{$line}")->get($this->language);
		}
		protected function replace($message, $attribute, $rule, $parameters)
		{
			$message = str_replace(':attribute', $this->attribute($attribute), $message);
			if (method_exists($this, $replacer = 'replace_' . $rule)) {
				$message = $this->$replacer($message, $attribute, $rule, $parameters);
			}
			return $message;
		}
		protected function replace_required_with($message, $attribute, $rule, $parameters)
		{
			return str_replace(':field', $this->attribute($parameters[0]), $message);
		}
		protected function replace_between($message, $attribute, $rule, $parameters)
		{
			return str_replace(array(':min', ':max'), $parameters, $message);
		}
		protected function replace_size($message, $attribute, $rule, $parameters)
		{
			return str_replace(':size', $parameters[0], $message);
		}
		protected function replace_min($message, $attribute, $rule, $parameters)
		{
			return str_replace(':min', $parameters[0], $message);
		}
		protected function replace_max($message, $attribute, $rule, $parameters)
		{
			return str_replace(':max', $parameters[0], $message);
		}
		protected function replace_in($message, $attribute, $rule, $parameters)
		{
			return str_replace(':values', implode(', ', $parameters), $message);
		}
		protected function replace_not_in($message, $attribute, $rule, $parameters)
		{
			return str_replace(':values', implode(', ', $parameters), $message);
		}
		protected function replace_mimes($message, $attribute, $rule, $parameters)
		{
			return str_replace(':values', implode(', ', $parameters), $message);
		}
		protected function replace_same($message, $attribute, $rule, $parameters)
		{
			return str_replace(':other', $this->attribute($parameters[0]), $message);
		}
		protected function replace_different($message, $attribute, $rule, $parameters)
		{
			return str_replace(':other', $this->attribute($parameters[0]), $message);
		}
		protected function replace_before($message, $attribute, $rule, $parameters)
		{
			return str_replace(':date', $parameters[0], $message);
		}
		protected function replace_after($message, $attribute, $rule, $parameters)
		{
			return str_replace(':date', $parameters[0], $message);
		}
		protected function replace_count($message, $attribute, $rule, $parameters)
		{
			return str_replace(':count', $parameters[0], $message);
		}
		protected function replace_countmin($message, $attribute, $rule, $parameters)
		{
			return str_replace(':min', $parameters[0], $message);
		}
		protected function replace_countmax($message, $attribute, $rule, $parameters)
		{
			return str_replace(':max', $parameters[0], $message);
		}
		protected function replace_countbetween($message, $attribute, $rule, $parameters)
		{
			return str_replace(array(':min', ':max'), $parameters, $message);
		}
		protected function attribute($attribute)
		{
			$bundle = Bundle::prefix($this->bundle);
			$line = "{$bundle}validation.attributes.{$attribute}";
			if (Lang::has($line, $this->language)) {
				return Lang::line($line)->get($this->language);
			} else {
				return str_replace('_', ' ', $attribute);
			}
		}
		protected function has_rule($attribute, $rules)
		{
			foreach ($this->rules[$attribute] as $rule) {
				list($rule, $parameters) = $this->parse($rule);
				if (in_array($rule, $rules)) return true;
			}
			return false;
		}
		protected function parse($rule)
		{
			$parameters = array();
			if (($colon = strpos($rule, ':')) !== false) {
				$parameters = str_getcsv(substr($rule, $colon + 1));
			}
			return array(is_numeric($colon) ? substr($rule, 0, $colon) : $rule, $parameters);
		}
		public function bundle($bundle)
		{
			$this->bundle = $bundle;
			return $this;
		}
		public function speaks($language)
		{
			$this->language = $language;
			return $this;
		}
		public function connection(Database\Connection $connection)
		{
			$this->db = $connection;
			return $this;
		}
		protected function db()
		{
			if (!is_null($this->db)) return $this->db;
			return $this->db = Database::connection();
		}
		public function __call($method, $parameters)
		{
			if (isset(static ::$validators[$method = substr($method, 9) ])) {
				return call_user_func_array(static ::$validators[$method], $parameters);
			}
			throw new \Exception("Method [$method] does not exist.");
		}
	}
}
namespace Laravel\Database
{
	use Closure;
	use Laravel\Database\Query\Grammars\Postgres;
	use Laravel\Fluent;
	use Laravel\Database as DB;
	use Laravel\Database;
	use Laravel\Config;
	use Laravel\Paginator;
	use PDO;
	use PDOStatement;
	use Laravel\Database\Query\Grammars\SQLServer;
	use Laravel\Event;
	/**
	 * laravel\database\connection.php
	 */
	class Connection
	{
		public $pdo;
		public $config;
		protected $grammar;
		public static $queries = array();
		public function __construct(PDO $pdo, $config)
		{
			$this->pdo = $pdo;
			$this->config = $config;
		}
		public function table($table)
		{
			return new Query($this, $this->grammar(), $table);
		}
		protected function grammar()
		{
			if (isset($this->grammar)) return $this->grammar;
			if (isset(\Laravel\Database::$registrar[$this->driver() ])) {
				return $this->grammar = \Laravel\Database::$registrar[$this->driver() ]['query']();
			}
			switch ($this->driver()) {
			case 'mysql':
				return $this->grammar = new Query\Grammars\MySQL($this);
			case 'sqlite':
				return $this->grammar = new Query\Grammars\SQLite($this);
			case 'sqlsrv':
				return $this->grammar = new Query\Grammars\SQLServer($this);
			case 'pgsql':
				return $this->grammar = new Query\Grammars\Postgres($this);
			default:
				return $this->grammar = new Query\Grammars\Grammar($this);
			}
		}
		public function transaction($callback)
		{
			$this->pdo->beginTransaction();
			try {
				call_user_func($callback);
			}
			catch(\Exception $e) {
				$this->pdo->rollBack();
				throw $e;
			}
			return $this->pdo->commit();
		}
		public function only($sql, $bindings = array())
		{
			$results = (array)$this->first($sql, $bindings);
			return reset($results);
		}
		public function first($sql, $bindings = array())
		{
			if (count($results = $this->query($sql, $bindings)) > 0) {
				return $results[0];
			}
		}
		public function query($sql, $bindings = array())
		{
			$sql = trim($sql);
			list($statement, $result) = $this->execute($sql, $bindings);
			if (stripos($sql, 'select') === 0 || stripos($sql, 'show') === 0) {
				return $this->fetch($statement, Config::get('database.fetch'));
			} elseif (stripos($sql, 'update') === 0 or stripos($sql, 'delete') === 0) {
				return $statement->rowCount();
			} elseif (stripos($sql, 'insert') === 0 and stripos($sql, 'returning') !== false) {
				return $this->fetch($statement, Config::get('database.fetch'));
			} else {
				return $result;
			}
		}
		protected function execute($sql, $bindings = array())
		{
			$bindings = (array)$bindings;
			$bindings = array_filter($bindings, function ($binding)
			{
				return !$binding instanceof Expression;
			});
			$bindings = array_values($bindings);
			$sql = $this->grammar()->shortcut($sql, $bindings);
			$datetime = $this->grammar()->datetime;
			for ($i = 0; $i < count($bindings); $i++) {
				if ($bindings[$i] instanceof \DateTime) {
					$bindings[$i] = $bindings[$i]->format($datetime);
				}
			}
			try {
				$statement = $this->pdo->prepare($sql);
				$start = microtime(true);
				$result = $statement->execute($bindings);
			}
			catch(\Exception $exception) {
				$exception = new Exception($sql, $bindings, $exception);
				throw $exception;
			}
			if (Config::get('database.profile')) {
				$this->log($sql, $bindings, $start);
			}
			return array($statement, $result);
		}
		protected function fetch($statement, $style)
		{
			if ($style === PDO::FETCH_CLASS) {
				return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
			} else {
				return $statement->fetchAll($style);
			}
		}
		protected function log($sql, $bindings, $start)
		{
			$time = number_format((microtime(true) - $start) * 1000, 2);
			Event::fire('laravel.query', array($sql, $bindings, $time));
			static ::$queries[] = compact('sql', 'bindings', 'time');
		}
		public function driver()
		{
			return $this->config['driver'];
		}
		public function __call($method, $parameters)
		{
			return $this->table($method);
		}
	}
	/**
	 * laravel\database\exception.php
	 */
	class Exception extends \Exception
	{
		protected $inner;
		public function __construct($sql, $bindings, \Exception $inner)
		{
			$this->inner = $inner;
			$this->setMessage($sql, $bindings);
			$this->code = $inner->getCode();
		}
		public function getInner()
		{
			return $this->inner;
		}
		protected function setMessage($sql, $bindings)
		{
			$this->message = $this->inner->getMessage();
			$this->message.= "\n\nSQL: " . $sql . "\n\nBindings: " . var_export($bindings, true);
		}
	}
	/**
	 * laravel\database\expression.php
	 */
	class Expression
	{
		protected $value;
		public function __construct($value)
		{
			$this->value = $value;
		}
		public function get()
		{
			return $this->value;
		}
		public function __toString()
		{
			return $this->get();
		}
	}
	/**
	 * laravel\database\grammar.php
	 */
	abstract class Grammar
	{
		protected $wrapper = '"%s"';
		protected $connection;
		public function __construct(Connection $connection)
		{
			$this->connection = $connection;
		}
		public function wrap_table($table)
		{
			if ($table instanceof Expression) {
				return $this->wrap($table);
			}
			$prefix = '';
			if (isset($this->connection->config['prefix'])) {
				$prefix = $this->connection->config['prefix'];
			}
			return $this->wrap($prefix . $table);
		}
		public function wrap($value)
		{
			if ($value instanceof Expression) {
				return $value->get();
			}
			if (strpos(strtolower($value), ' as ') !== false) {
				$segments = explode(' ', $value);
				return sprintf('%s AS %s', $this->wrap($segments[0]), $this->wrap($segments[2]));
			}
			$segments = explode('.', $value);
			foreach ($segments as $key => $value) {
				if ($key == 0 and count($segments) > 1) {
					$wrapped[] = $this->wrap_table($value);
				} else {
					$wrapped[] = $this->wrap_value($value);
				}
			}
			return implode('.', $wrapped);
		}
		protected function wrap_value($value)
		{
			return ($value !== '*') ? sprintf($this->wrapper, $value) : $value;
		}
		final public function parameterize($values)
		{
			return implode(', ', array_map(array($this, 'parameter'), $values));
		}
		final public function parameter($value)
		{
			return ($value instanceof Expression) ? $value->get() : '?';
		}
		final public function columnize($columns)
		{
			return implode(', ', array_map(array($this, 'wrap'), $columns));
		}
	}
	/**
	 * laravel\database\query.php
	 */
	class Query
	{
		public $connection;
		public $grammar;
		public $selects;
		public $aggregate;
		public $distinct = false;
		public $from;
		public $joins;
		public $wheres;
		public $groupings;
		public $havings;
		public $orderings;
		public $limit;
		public $offset;
		public $bindings = array();
		public function __construct(Connection $connection, Query\Grammars\Grammar $grammar, $table)
		{
			$this->from = $table;
			$this->grammar = $grammar;
			$this->connection = $connection;
		}
		public function distinct()
		{
			$this->distinct = true;
			return $this;
		}
		public function select($columns = array('*'))
		{
			$this->selects = (array)$columns;
			return $this;
		}
		public function join($table, $column1, $operator = null, $column2 = null, $type = 'INNER')
		{
			if ($column1 instanceof Closure) {
				$this->joins[] = new Query\Join($type, $table);
				call_user_func($column1, end($this->joins));
			} else {
				$join = new Query\Join($type, $table);
				$join->on($column1, $operator, $column2);
				$this->joins[] = $join;
			}
			return $this;
		}
		public function left_join($table, $column1, $operator = null, $column2 = null)
		{
			return $this->join($table, $column1, $operator, $column2, 'LEFT');
		}
		public function reset_where()
		{
			list($this->wheres, $this->bindings) = array(array(), array());
		}
		public function raw_where($where, $bindings = array(), $connector = 'AND')
		{
			$this->wheres[] = array('type' => 'where_raw', 'connector' => $connector, 'sql' => $where);
			$this->bindings = array_merge($this->bindings, $bindings);
			return $this;
		}
		public function raw_or_where($where, $bindings = array())
		{
			return $this->raw_where($where, $bindings, 'OR');
		}
		public function where($column, $operator = null, $value = null, $connector = 'AND')
		{
			if ($column instanceof Closure) {
				return $this->where_nested($column, $connector);
			}
			$type = 'where';
			$this->wheres[] = compact('type', 'column', 'operator', 'value', 'connector');
			$this->bindings[] = $value;
			return $this;
		}
		public function or_where($column, $operator = null, $value = null)
		{
			return $this->where($column, $operator, $value, 'OR');
		}
		public function or_where_id($value)
		{
			return $this->or_where('id', '=', $value);
		}
		public function where_in($column, $values, $connector = 'AND', $not = false)
		{
			$type = ($not) ? 'where_not_in' : 'where_in';
			$this->wheres[] = compact('type', 'column', 'values', 'connector');
			$this->bindings = array_merge($this->bindings, $values);
			return $this;
		}
		public function or_where_in($column, $values)
		{
			return $this->where_in($column, $values, 'OR');
		}
		public function where_not_in($column, $values, $connector = 'AND')
		{
			return $this->where_in($column, $values, $connector, true);
		}
		public function or_where_not_in($column, $values)
		{
			return $this->where_not_in($column, $values, 'OR');
		}
		public function where_between($column, $min, $max, $connector = 'AND', $not = false)
		{
			$type = ($not) ? 'where_not_between' : 'where_between';
			$this->wheres[] = compact('type', 'column', 'min', 'max', 'connector');
			$this->bindings[] = $min;
			$this->bindings[] = $max;
			return $this;
		}
		public function or_where_between($column, $min, $max)
		{
			return $this->where_between($column, $min, $max, 'OR');
		}
		public function where_not_between($column, $min, $max, $connector = 'AND')
		{
			return $this->where_between($column, $min, $max, $connector, true);
		}
		public function or_where_not_between($column, $min, $max)
		{
			return $this->where_not_between($column, $min, $max, 'OR');
		}
		public function where_null($column, $connector = 'AND', $not = false)
		{
			$type = ($not) ? 'where_not_null' : 'where_null';
			$this->wheres[] = compact('type', 'column', 'connector');
			return $this;
		}
		public function or_where_null($column)
		{
			return $this->where_null($column, 'OR');
		}
		public function where_not_null($column, $connector = 'AND')
		{
			return $this->where_null($column, $connector, true);
		}
		public function or_where_not_null($column)
		{
			return $this->where_not_null($column, 'OR');
		}
		public function where_nested($callback, $connector = 'AND')
		{
			$type = 'where_nested';
			$query = new Query($this->connection, $this->grammar, $this->from);
			call_user_func($callback, $query);
			if ($query->wheres !== null) {
				$this->wheres[] = compact('type', 'query', 'connector');
			}
			$this->bindings = array_merge($this->bindings, $query->bindings);
			return $this;
		}
		private function dynamic_where($method, $parameters)
		{
			$finder = substr($method, 6);
			$flags = PREG_SPLIT_DELIM_CAPTURE;
			$segments = preg_split('/(_and_|_or_)/i', $finder, -1, $flags);
			$connector = 'AND';
			$index = 0;
			foreach ($segments as $segment) {
				if ($segment != '_and_' and $segment != '_or_') {
					$this->where($segment, '=', $parameters[$index], $connector);
					$index++;
				} else {
					$connector = trim(strtoupper($segment), '_');
				}
			}
			return $this;
		}
		public function group_by($column)
		{
			$this->groupings[] = $column;
			return $this;
		}
		public function having($column, $operator, $value)
		{
			$this->havings[] = compact('column', 'operator', 'value');
			$this->bindings[] = $value;
			return $this;
		}
		public function order_by($column, $direction = 'asc')
		{
			$this->orderings[] = compact('column', 'direction');
			return $this;
		}
		public function skip($value)
		{
			$this->offset = $value;
			return $this;
		}
		public function take($value)
		{
			$this->limit = $value;
			return $this;
		}
		public function for_page($page, $per_page)
		{
			return $this->skip(($page - 1) * $per_page)->take($per_page);
		}
		public function find($id, $columns = array('*'))
		{
			return $this->where('id', '=', $id)->first($columns);
		}
		public function only($column)
		{
			$sql = $this->grammar->select($this->select(array($column)));
			return $this->connection->only($sql, $this->bindings);
		}
		public function first($columns = array('*'))
		{
			$columns = (array)$columns;
			$results = $this->take(1)->get($columns);
			return (count($results) > 0) ? $results[0] : null;
		}
		public function lists($column, $key = null)
		{
			$columns = (is_null($key)) ? array($column) : array($column, $key);
			$results = $this->get($columns);
			$values = array_map(function ($row) use ($column)
			{
				return $row->$column;
			}, $results);
			if (!is_null($key) && count($results)) {
				return array_combine(array_map(function ($row) use ($key)
				{
					return $row->$key;
				}, $results), $values);
			}
			return $values;
		}
		public function get($columns = array('*'))
		{
			if (is_null($this->selects)) $this->select($columns);
			$sql = $this->grammar->select($this);
			$results = $this->connection->query($sql, $this->bindings);
			if ($this->offset > 0 and $this->grammar instanceof SQLServer) {
				array_walk($results, function ($result)
				{
					unset($result->rownum);
				});
			}
			$this->selects = null;
			return $results;
		}
		public function aggregate($aggregator, $columns)
		{
			$this->aggregate = compact('aggregator', 'columns');
			$sql = $this->grammar->select($this);
			$result = $this->connection->only($sql, $this->bindings);
			$this->aggregate = null;
			return $result;
		}
		public function paginate($per_page = 20, $columns = array('*'))
		{
			list($orderings, $this->orderings) = array($this->orderings, null);
			$total = $this->count(reset($columns));
			$page = Paginator::page($total, $per_page);
			$this->orderings = $orderings;
			$results = $this->for_page($page, $per_page)->get($columns);
			return Paginator::make($results, $total, $per_page);
		}
		public function insert($values)
		{
			if (!is_array(reset($values))) $values = array($values);
			$bindings = array();
			foreach ($values as $value) {
				$bindings = array_merge($bindings, array_values($value));
			}
			$sql = $this->grammar->insert($this, $values);
			return $this->connection->query($sql, $bindings);
		}
		public function insert_get_id($values, $column = 'id')
		{
			$sql = $this->grammar->insert_get_id($this, $values, $column);
			$result = $this->connection->query($sql, array_values($values));
			if (isset($values[$column])) {
				return $values[$column];
			} else if ($this->grammar instanceof Postgres) {
				return (int)$result[0]->$column;
			} else {
				return (int)$this->connection->pdo->lastInsertId();
			}
		}
		public function increment($column, $amount = 1)
		{
			return $this->adjust($column, $amount, ' + ');
		}
		public function decrement($column, $amount = 1)
		{
			return $this->adjust($column, $amount, ' - ');
		}
		protected function adjust($column, $amount, $operator)
		{
			$wrapped = $this->grammar->wrap($column);
			$value = Database::raw($wrapped . $operator . $amount);
			return $this->update(array($column => $value));
		}
		public function update($values)
		{
			$bindings = array_merge(array_values($values), $this->bindings);
			$sql = $this->grammar->update($this, $values);
			return $this->connection->query($sql, $bindings);
		}
		public function delete($id = null)
		{
			if (!is_null($id)) {
				$this->where('id', '=', $id);
			}
			$sql = $this->grammar->delete($this);
			return $this->connection->query($sql, $this->bindings);
		}
		public function __call($method, $parameters)
		{
			if (strpos($method, 'where_') === 0) {
				return $this->dynamic_where($method, $parameters, $this);
			}
			if (in_array($method, array('count', 'min', 'max', 'avg', 'sum'))) {
				if (count($parameters) == 0) $parameters[0] = '*';
				return $this->aggregate(strtoupper($method), (array)$parameters[0]);
			}
			throw new \Exception("Method [$method] is not defined on the Query class.");
		}
	}
	/**
	 * laravel\database\schema.php
	 */
	class Schema
	{
		public static function table($table, $callback)
		{
			call_user_func($callback, $table = new Schema\Table($table));
			return static ::execute($table);
		}
		public static function create($table, $callback)
		{
			$table = new Schema\Table($table);
			$table->create();
			call_user_func($callback, $table);
			return static ::execute($table);
		}
		public static function rename($table, $new_name)
		{
			$table = new Schema\Table($table);
			$table->rename($new_name);
			return static ::execute($table);
		}
		public static function drop($table, $connection = null)
		{
			$table = new Schema\Table($table);
			$table->on($connection);
			$table->drop();
			return static ::execute($table);
		}
		public static function execute($table)
		{
			static ::implications($table);
			foreach ($table->commands as $command) {
				$connection = DB::connection($table->connection);
				$grammar = static ::grammar($connection);
				if (method_exists($grammar, $method = $command->type)) {
					$statements = $grammar->$method($table, $command);
					foreach ((array)$statements as $statement) {
						$connection->query($statement);
					}
				}
			}
		}
		protected static function implications($table)
		{
			if (count($table->columns) > 0 and !$table->creating()) {
				$command = new Fluent(array('type' => 'add'));
				array_unshift($table->commands, $command);
			}
			foreach ($table->columns as $column) {
				foreach (array('primary', 'unique', 'fulltext', 'index') as $key) {
					if (isset($column->$key)) {
						if ($column->$key === true) {
							$table->$key($column->name);
						} else {
							$table->$key($column->name, $column->$key);
						}
					}
				}
			}
		}
		public static function grammar(Connection $connection)
		{
			$driver = $connection->driver();
			if (isset(\Laravel\Database::$registrar[$driver])) {
				return \Laravel\Database::$registrar[$driver]['schema']();
			}
			switch ($driver) {
			case 'mysql':
				return new Schema\Grammars\MySQL($connection);
			case 'pgsql':
				return new Schema\Grammars\Postgres($connection);
			case 'sqlsrv':
				return new Schema\Grammars\SQLServer($connection);
			case 'sqlite':
				return new Schema\Grammars\SQLite($connection);
			}
			throw new \Exception("Schema operations not supported for [$driver].");
		}
	}
}
namespace Symfony\Component\HttpFoundation\Session\Storage\Handler
{
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeSessionHandler.php
	 */
	if (version_compare(phpversion(), '5.4.0', '>=')) {
		class NativeSessionHandler extends \SessionHandler
		{
		}
	} else {
		class NativeSessionHandler
		{
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler.php
	 */
	class MemcachedSessionHandler implements \SessionHandlerInterface
	{
		private $memcached;
		private $ttl;
		private $prefix;
		public function __construct(\Memcached $memcached, array $options = array())
		{
			$this->memcached = $memcached;
			if ($diff = array_diff(array_keys($options), array('prefix', 'expiretime'))) {
				throw new \InvalidArgumentException(sprintf('The following options are not supported "%s"', implode(', ', $diff)));
			}
			$this->ttl = isset($options['expiretime']) ? (int)$options['expiretime'] : 86400;
			$this->prefix = isset($options['prefix']) ? $options['prefix'] : 'sf2s';
		}
		public function open($savePath, $sessionName)
		{
			return true;
		}
		public function close()
		{
			return true;
		}
		public function read($sessionId)
		{
			return $this->memcached->get($this->prefix . $sessionId) ? : '';
		}
		public function write($sessionId, $data)
		{
			return $this->memcached->set($this->prefix . $sessionId, $data, time() + $this->ttl);
		}
		public function destroy($sessionId)
		{
			return $this->memcached->delete($this->prefix . $sessionId);
		}
		public function gc($lifetime)
		{
			return true;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcacheSessionHandler.php
	 */
	class MemcacheSessionHandler implements \SessionHandlerInterface
	{
		private $memcache;
		private $ttl;
		private $prefix;
		public function __construct(\Memcache $memcache, array $options = array())
		{
			if ($diff = array_diff(array_keys($options), array('prefix', 'expiretime'))) {
				throw new \InvalidArgumentException(sprintf('The following options are not supported "%s"', implode(', ', $diff)));
			}
			$this->memcache = $memcache;
			$this->ttl = isset($options['expiretime']) ? (int)$options['expiretime'] : 86400;
			$this->prefix = isset($options['prefix']) ? $options['prefix'] : 'sf2s';
		}
		public function open($savePath, $sessionName)
		{
			return true;
		}
		public function close()
		{
			return $this->memcache->close();
		}
		public function read($sessionId)
		{
			return $this->memcache->get($this->prefix . $sessionId) ? : '';
		}
		public function write($sessionId, $data)
		{
			return $this->memcache->set($this->prefix . $sessionId, $data, 0, time() + $this->ttl);
		}
		public function destroy($sessionId)
		{
			return $this->memcache->delete($this->prefix . $sessionId);
		}
		public function gc($lifetime)
		{
			return true;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Handler\MongoDbSessionHandler.php
	 */
	class MongoDbSessionHandler implements \SessionHandlerInterface
	{
		private $mongo;
		private $collection;
		private $options;
		public function __construct($mongo, array $options)
		{
			if (!($mongo instanceof \MongoClient || $mongo instanceof \Mongo)) {
				throw new \InvalidArgumentException('MongoClient or Mongo instance required');
			}
			if (!isset($options['database']) || !isset($options['collection'])) {
				throw new \InvalidArgumentException('You must provide the "database" and "collection" option for MongoDBSessionHandler');
			}
			$this->mongo = $mongo;
			$this->options = array_merge(array('id_field' => 'sess_id', 'data_field' => 'sess_data', 'time_field' => 'sess_time',), $options);
		}
		public function open($savePath, $sessionName)
		{
			return true;
		}
		public function close()
		{
			return true;
		}
		public function destroy($sessionId)
		{
			$this->getCollection()->remove(array($this->options['id_field'] => $sessionId), array('justOne' => true));
			return true;
		}
		public function gc($lifetime)
		{
			$time = new \MongoTimestamp(time() - $lifetime);
			$this->getCollection()->remove(array($this->options['time_field'] => array('$lt' => $time),));
		}
		public function write($sessionId, $data)
		{
			$data = array($this->options['id_field'] => $sessionId, $this->options['data_field'] => new \MongoBinData($data, \MongoBinData::BYTE_ARRAY), $this->options['time_field'] => new \MongoTimestamp());
			$this->getCollection()->update(array($this->options['id_field'] => $sessionId), array('$set' => $data), array('upsert' => true));
			return true;
		}
		public function read($sessionId)
		{
			$dbData = $this->getCollection()->findOne(array($this->options['id_field'] => $sessionId,));
			return null === $dbData ? '' : $dbData[$this->options['data_field']]->bin;
		}
		private function getCollection()
		{
			if (null === $this->collection) {
				$this->collection = $this->mongo->selectCollection($this->options['database'], $this->options['collection']);
			}
			return $this->collection;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler.php
	 */
	class NativeFileSessionHandler extends NativeSessionHandler
	{
		public function __construct($savePath = null)
		{
			if (null === $savePath) {
				$savePath = ini_get('session.save_path');
			}
			$baseDir = $savePath;
			if ($count = substr_count($savePath, ';')) {
				if ($count > 2) {
					throw new \InvalidArgumentException(sprintf('Invalid argument $savePath \'%s\'', $savePath));
				}
				$baseDir = ltrim(strrchr($savePath, ';'), ';');
			}
			if ($baseDir && !is_dir($baseDir)) {
				mkdir($baseDir, 0777, true);
			}
			ini_set('session.save_path', $savePath);
			ini_set('session.save_handler', 'files');
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler.php
	 */
	class NullSessionHandler implements \SessionHandlerInterface
	{
		public function open($savePath, $sessionName)
		{
			return true;
		}
		public function close()
		{
			return true;
		}
		public function read($sessionId)
		{
			return '';
		}
		public function write($sessionId, $data)
		{
			return true;
		}
		public function destroy($sessionId)
		{
			return true;
		}
		public function gc($lifetime)
		{
			return true;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler.php
	 */
	class PdoSessionHandler implements \SessionHandlerInterface
	{
		private $pdo;
		private $dbOptions;
		public function __construct(\PDO $pdo, array $dbOptions = array())
		{
			if (!array_key_exists('db_table', $dbOptions)) {
				throw new \InvalidArgumentException('You must provide the "db_table" option for a PdoSessionStorage.');
			}
			$this->pdo = $pdo;
			$this->dbOptions = array_merge(array('db_id_col' => 'sess_id', 'db_data_col' => 'sess_data', 'db_time_col' => 'sess_time',), $dbOptions);
		}
		public function open($path, $name)
		{
			return true;
		}
		public function close()
		{
			return true;
		}
		public function destroy($id)
		{
			$dbTable = $this->dbOptions['db_table'];
			$dbIdCol = $this->dbOptions['db_id_col'];
			$sql = "DELETE FROM $dbTable WHERE $dbIdCol = :id";
			try {
				$stmt = $this->pdo->prepare($sql);
				$stmt->bindParam(':id', $id, \PDO::PARAM_STR);
				$stmt->execute();
			}
			catch(\PDOException $e) {
				throw new \RuntimeException(sprintf('PDOException was thrown when trying to manipulate session data: %s', $e->getMessage()), 0, $e);
			}
			return true;
		}
		public function gc($lifetime)
		{
			$dbTable = $this->dbOptions['db_table'];
			$dbTimeCol = $this->dbOptions['db_time_col'];
			$sql = "DELETE FROM $dbTable WHERE $dbTimeCol < :time";
			try {
				$stmt = $this->pdo->prepare($sql);
				$stmt->bindValue(':time', time() - $lifetime, \PDO::PARAM_INT);
				$stmt->execute();
			}
			catch(\PDOException $e) {
				throw new \RuntimeException(sprintf('PDOException was thrown when trying to manipulate session data: %s', $e->getMessage()), 0, $e);
			}
			return true;
		}
		public function read($id)
		{
			$dbTable = $this->dbOptions['db_table'];
			$dbDataCol = $this->dbOptions['db_data_col'];
			$dbIdCol = $this->dbOptions['db_id_col'];
			try {
				$sql = "SELECT $dbDataCol FROM $dbTable WHERE $dbIdCol = :id";
				$stmt = $this->pdo->prepare($sql);
				$stmt->bindParam(':id', $id, \PDO::PARAM_STR);
				$stmt->execute();
				$sessionRows = $stmt->fetchAll(\PDO::FETCH_NUM);
				if (count($sessionRows) == 1) {
					return base64_decode($sessionRows[0][0]);
				}
				$this->createNewSession($id);
				return '';
			}
			catch(\PDOException $e) {
				throw new \RuntimeException(sprintf('PDOException was thrown when trying to read the session data: %s', $e->getMessage()), 0, $e);
			}
		}
		public function write($id, $data)
		{
			$dbTable = $this->dbOptions['db_table'];
			$dbDataCol = $this->dbOptions['db_data_col'];
			$dbIdCol = $this->dbOptions['db_id_col'];
			$dbTimeCol = $this->dbOptions['db_time_col'];
			$encoded = base64_encode($data);
			try {
				$driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
				if ('mysql' === $driver) {
					$stmt = $this->pdo->prepare("INSERT INTO $dbTable ($dbIdCol, $dbDataCol, $dbTimeCol) VALUES (:id, :data, :time) " . "ON DUPLICATE KEY UPDATE $dbDataCol = VALUES($dbDataCol), $dbTimeCol = VALUES($dbTimeCol)");
					$stmt->bindParam(':id', $id, \PDO::PARAM_STR);
					$stmt->bindParam(':data', $encoded, \PDO::PARAM_STR);
					$stmt->bindValue(':time', time(), \PDO::PARAM_INT);
					$stmt->execute();
				} elseif ('oci' === $driver) {
					$stmt = $this->pdo->prepare("MERGE INTO $dbTable USING DUAL ON($dbIdCol = :id) " . "WHEN NOT MATCHED THEN INSERT ($dbIdCol, $dbDataCol, $dbTimeCol) VALUES (:id, :data, sysdate) " . "WHEN MATCHED THEN UPDATE SET $dbDataCol = :data WHERE $dbIdCol = :id");
					$stmt->bindParam(':id', $id, \PDO::PARAM_STR);
					$stmt->bindParam(':data', $encoded, \PDO::PARAM_STR);
					$stmt->execute();
				} else {
					$stmt = $this->pdo->prepare("UPDATE $dbTable SET $dbDataCol = :data, $dbTimeCol = :time WHERE $dbIdCol = :id");
					$stmt->bindParam(':id', $id, \PDO::PARAM_STR);
					$stmt->bindParam(':data', $encoded, \PDO::PARAM_STR);
					$stmt->bindValue(':time', time(), \PDO::PARAM_INT);
					$stmt->execute();
					if (!$stmt->rowCount()) {
						$this->createNewSession($id, $data);
					}
				}
			}
			catch(\PDOException $e) {
				throw new \RuntimeException(sprintf('PDOException was thrown when trying to write the session data: %s', $e->getMessage()), 0, $e);
			}
			return true;
		}
		private function createNewSession($id, $data = '')
		{
			$dbTable = $this->dbOptions['db_table'];
			$dbDataCol = $this->dbOptions['db_data_col'];
			$dbIdCol = $this->dbOptions['db_id_col'];
			$dbTimeCol = $this->dbOptions['db_time_col'];
			$sql = "INSERT INTO $dbTable ($dbIdCol, $dbDataCol, $dbTimeCol) VALUES (:id, :data, :time)";
			$encoded = base64_encode($data);
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindParam(':id', $id, \PDO::PARAM_STR);
			$stmt->bindParam(':data', $encoded, \PDO::PARAM_STR);
			$stmt->bindValue(':time', time(), \PDO::PARAM_INT);
			$stmt->execute();
			return true;
		}
	}
}
namespace Laravel\Database\Connectors
{
	use PDO;
	/**
	 * laravel\database\connectors\connector.php
	 */
	abstract class Connector
	{
		protected $options = array(PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL, PDO::ATTR_STRINGIFY_FETCHES => false, PDO::ATTR_EMULATE_PREPARES => false,);
		abstract public function connect($config);
		protected function options($config)
		{
			$options = (isset($config['options'])) ? $config['options'] : array();
			return $options + $this->options;
		}
	}
	/**
	 * laravel\database\connectors\mysql.php
	 */
	class MySQL extends Connector
	{
		public function connect($config)
		{
			extract($config);
			$dsn = "mysql:host={$host};dbname={$database}";
			if (isset($config['port'])) {
				$dsn.= ";port={$config['port']}";
			}
			if (isset($config['unix_socket'])) {
				$dsn.= ";unix_socket={$config['unix_socket']}";
			}
			$connection = new PDO($dsn, $username, $password, $this->options($config));
			if (isset($config['charset'])) {
				$connection->prepare("SET NAMES '{$config['charset']}'")->execute();
			}
			return $connection;
		}
	}
	/**
	 * laravel\database\connectors\postgres.php
	 */
	class Postgres extends Connector
	{
		protected $options = array(PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL, PDO::ATTR_STRINGIFY_FETCHES => false,);
		public function connect($config)
		{
			extract($config);
			$host_dsn = isset($host) ? 'host=' . $host . ';' : '';
			$dsn = "pgsql:{$host_dsn}dbname={$database}";
			if (isset($config['port'])) {
				$dsn.= ";port={$config['port']}";
			}
			$connection = new PDO($dsn, $username, $password, $this->options($config));
			if (isset($config['charset'])) {
				$connection->prepare("SET NAMES '{$config['charset']}'")->execute();
			}
			if (isset($config['schema'])) {
				$connection->prepare("SET search_path TO {$config['schema']}")->execute();
			}
			return $connection;
		}
	}
	/**
	 * laravel\database\connectors\sqlite.php
	 */
	class SQLite extends Connector
	{
		public function connect($config)
		{
			$options = $this->options($config);
			if ($config['database'] == ':memory:') {
				return new PDO('sqlite::memory:', null, null, $options);
			}
			$path = path('storage') . 'database' . DS . $config['database'] . '.sqlite';
			return new PDO('sqlite:' . $path, null, null, $options);
		}
	}
	/**
	 * laravel\database\connectors\sqlserver.php
	 */
	class SQLServer extends Connector
	{
		protected $options = array(PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL, PDO::ATTR_STRINGIFY_FETCHES => false,);
		public function connect($config)
		{
			extract($config);
			$port = (isset($port)) ? ',' . $port : '';
			if (in_array('dblib', PDO::getAvailableDrivers())) {
				$dsn = "dblib:host={$host}{$port};dbname={$database}";
			} else {
				$dsn = "sqlsrv:Server={$host}{$port};Database={$database}";
			}
			return new PDO($dsn, $username, $password, $this->options($config));
		}
	}
}
namespace Laravel\Auth\Drivers
{
	use Laravel\Database as DB;
	use Laravel\Config;
	use Laravel\Crypter;
	use Laravel\Hash;
	use Laravel\Session;
	use Laravel\Cookie;
	use Laravel\Str;
	use Laravel\Event;
	/**
	 * laravel\auth\drivers\driver.php
	 */
	abstract class Driver
	{
		public $user;
		public $token;
		public function __construct()
		{
			if (Session::started()) {
				$this->token = Session::get($this->token());
			}
			if (is_null($this->token)) {
				$this->token = $this->recall();
			}
		}
		public function guest()
		{
			return !$this->check();
		}
		public function check()
		{
			return !is_null($this->user());
		}
		public function user()
		{
			if (!is_null($this->user)) return $this->user;
			return $this->user = $this->retrieve($this->token);
		}
		abstract public function retrieve($id);
		abstract public function attempt($arguments = array());
		public function login($token, $remember = false)
		{
			$this->token = $token;
			$this->store($token);
			if ($remember) $this->remember($token);
			Event::fire('laravel.auth: login');
			return true;
		}
		public function logout()
		{
			$this->user = null;
			$this->cookie($this->recaller(), null, -2000);
			Session::forget($this->token());
			Event::fire('laravel.auth: logout');
			$this->token = null;
		}
		protected function store($token)
		{
			Session::put($this->token(), $token);
		}
		protected function remember($token)
		{
			$token = Crypter::encrypt($token . '|' . Str::random(40));
			$this->cookie($this->recaller(), $token, Cookie::forever);
		}
		protected function recall()
		{
			$cookie = Cookie::get($this->recaller());
			if (!is_null($cookie)) {
				return head(explode('|', Crypter::decrypt($cookie)));
			}
		}
		protected function cookie($name, $value, $minutes)
		{
			$config = Config::get('session');
			extract($config);
			Cookie::put($name, $value, $minutes, $path, $domain, $secure);
		}
		protected function token()
		{
			return $this->name() . '_login';
		}
		protected function recaller()
		{
			return $this->name() . '_remember';
		}
		protected function name()
		{
			return strtolower(str_replace('\\', '_', get_class($this)));
		}
	}
	/**
	 * laravel\auth\drivers\eloquent.php
	 */
	class Eloquent extends Driver
	{
		public function retrieve($token)
		{
			if (filter_var($token, FILTER_VALIDATE_INT) !== false) {
				return $this->model()->find($token);
			} else if (is_object($token) and get_class($token) == Config::get('auth.model')) {
				return $token;
			}
		}
		public function attempt($arguments = array())
		{
			$user = $this->model()->where(function ($query) use ($arguments)
			{
				$username = Config::get('auth.username');
				$query->where($username, '=', $arguments['username']);
				foreach (array_except($arguments, array('username', 'password', 'remember')) as $column => $val) {
					$query->where($column, '=', $val);
				}
			})->first();
			$password = $arguments['password'];
			$password_field = Config::get('auth.password', 'password');
			if (!is_null($user) and Hash::check($password, $user->{$password_field})) {
				return $this->login($user->get_key(), array_get($arguments, 'remember'));
			}
			return false;
		}
		protected function model()
		{
			$model = Config::get('auth.model');
			return new $model;
		}
	}
	/**
	 * laravel\auth\drivers\fluent.php
	 */
	class Fluent extends Driver
	{
		public function retrieve($id)
		{
			if (filter_var($id, FILTER_VALIDATE_INT) !== false) {
				return DB::table(Config::get('auth.table'))->find($id);
			}
		}
		public function attempt($arguments = array())
		{
			$user = $this->get_user($arguments);
			$password = $arguments['password'];
			$password_field = Config::get('auth.password', 'password');
			if (!is_null($user) and Hash::check($password, $user->{$password_field})) {
				return $this->login($user->id, array_get($arguments, 'remember'));
			}
			return false;
		}
		protected function get_user($arguments)
		{
			$table = Config::get('auth.table');
			return DB::table($table)->where(function ($query) use ($arguments)
			{
				$username = Config::get('auth.username');
				$query->where($username, '=', $arguments['username']);
				foreach (array_except($arguments, array('username', 'password', 'remember')) as $column => $val) {
					$query->where($column, '=', $val);
				}
			})->first();
		}
	}
}
namespace Symfony\Component\HttpFoundation\Session\Storage\Proxy
{
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Proxy\AbstractProxy.php
	 */
	abstract class AbstractProxy
	{
		protected $wrapper = false;
		protected $active = false;
		protected $saveHandlerName;
		public function getSaveHandlerName()
		{
			return $this->saveHandlerName;
		}
		public function isSessionHandlerInterface()
		{
			return ($this instanceof \SessionHandlerInterface);
		}
		public function isWrapper()
		{
			return $this->wrapper;
		}
		public function isActive()
		{
			return $this->active;
		}
		public function setActive($flag)
		{
			$this->active = (bool)$flag;
		}
		public function getId()
		{
			return session_id();
		}
		public function setId($id)
		{
			if ($this->isActive()) {
				throw new \LogicException('Cannot change the ID of an active session');
			}
			session_id($id);
		}
		public function getName()
		{
			return session_name();
		}
		public function setName($name)
		{
			if ($this->isActive()) {
				throw new \LogicException('Cannot change the name of an active session');
			}
			session_name($name);
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Proxy\NativeProxy.php
	 */
	class NativeProxy extends AbstractProxy
	{
		public function __construct()
		{
			$this->saveHandlerName = ini_get('session.save_handler');
		}
		public function isWrapper()
		{
			return false;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy.php
	 */
	class SessionHandlerProxy extends AbstractProxy implements \SessionHandlerInterface
	{
		protected $handler;
		public function __construct(\SessionHandlerInterface $handler)
		{
			$this->handler = $handler;
			$this->wrapper = ($handler instanceof \SessionHandler);
			$this->saveHandlerName = $this->wrapper ? ini_get('session.save_handler') : 'user';
		}
		public function open($savePath, $sessionName)
		{
			$return = (bool)$this->handler->open($savePath, $sessionName);
			if (true === $return) {
				$this->active = true;
			}
			return $return;
		}
		public function close()
		{
			$this->active = false;
			return (bool)$this->handler->close();
		}
		public function read($id)
		{
			return (string)$this->handler->read($id);
		}
		public function write($id, $data)
		{
			return (bool)$this->handler->write($id, $data);
		}
		public function destroy($id)
		{
			return (bool)$this->handler->destroy($id);
		}
		public function gc($maxlifetime)
		{
			return (bool)$this->handler->gc($maxlifetime);
		}
	}
}
namespace Symfony\Component\HttpFoundation\Session\Storage
{
	use Symfony\Component\HttpFoundation\Session\Storage\Proxy\AbstractProxy;
	use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
	use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
	use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;
	use Symfony\Component\HttpFoundation\Session\Storage\Proxy\NativeProxy;
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\MetadataBag.php
	 */
	class MetadataBag implements SessionBagInterface
	{
		const CREATED = 'c';
		const UPDATED = 'u';
		const LIFETIME = 'l';
		private $name = '__metadata';
		private $storageKey;
		protected $meta = array();
		private $lastUsed;
		public function __construct($storageKey = '_sf2_meta')
		{
			$this->storageKey = $storageKey;
			$this->meta = array(self::CREATED => 0, self::UPDATED => 0, self::LIFETIME => 0);
		}
		public function initialize(array & $array)
		{
			$this->meta = & $array;
			if (isset($array[self::CREATED])) {
				$this->lastUsed = $this->meta[self::UPDATED];
				$this->meta[self::UPDATED] = time();
			} else {
				$this->stampCreated();
			}
		}
		public function getLifetime()
		{
			return $this->meta[self::LIFETIME];
		}
		public function stampNew($lifetime = null)
		{
			$this->stampCreated($lifetime);
		}
		public function getStorageKey()
		{
			return $this->storageKey;
		}
		public function getCreated()
		{
			return $this->meta[self::CREATED];
		}
		public function getLastUsed()
		{
			return $this->lastUsed;
		}
		public function clear()
		{
		}
		public function getName()
		{
			return $this->name;
		}
		public function setName($name)
		{
			$this->name = $name;
		}
		private function stampCreated($lifetime = null)
		{
			$timeStamp = time();
			$this->meta[self::CREATED] = $this->meta[self::UPDATED] = $this->lastUsed = $timeStamp;
			$this->meta[self::LIFETIME] = (null === $lifetime) ? ini_get('session.cookie_lifetime') : $lifetime;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage.php
	 */
	class MockArraySessionStorage implements SessionStorageInterface
	{
		protected $id = '';
		protected $name;
		protected $started = false;
		protected $closed = false;
		protected $data = array();
		protected $metadataBag;
		protected $bags;
		public function __construct($name = 'MOCKSESSID', MetadataBag $metaBag = null)
		{
			$this->name = $name;
			$this->setMetadataBag($metaBag);
		}
		public function setSessionData(array $array)
		{
			$this->data = $array;
		}
		public function start()
		{
			if ($this->started && !$this->closed) {
				return true;
			}
			if (empty($this->id)) {
				$this->id = $this->generateId();
			}
			$this->loadSession();
			return true;
		}
		public function regenerate($destroy = false, $lifetime = null)
		{
			if (!$this->started) {
				$this->start();
			}
			$this->metadataBag->stampNew($lifetime);
			$this->id = $this->generateId();
			return true;
		}
		public function getId()
		{
			return $this->id;
		}
		public function setId($id)
		{
			if ($this->started) {
				throw new \LogicException('Cannot set session ID after the session has started.');
			}
			$this->id = $id;
		}
		public function getName()
		{
			return $this->name;
		}
		public function setName($name)
		{
			$this->name = $name;
		}
		public function save()
		{
			if (!$this->started || $this->closed) {
				throw new \RuntimeException("Trying to save a session that was not started yet or was already closed");
			}
			$this->closed = false;
		}
		public function clear()
		{
			foreach ($this->bags as $bag) {
				$bag->clear();
			}
			$this->data = array();
			$this->loadSession();
		}
		public function registerBag(SessionBagInterface $bag)
		{
			$this->bags[$bag->getName() ] = $bag;
		}
		public function getBag($name)
		{
			if (!isset($this->bags[$name])) {
				throw new \InvalidArgumentException(sprintf('The SessionBagInterface %s is not registered.', $name));
			}
			if (!$this->started) {
				$this->start();
			}
			return $this->bags[$name];
		}
		public function isStarted()
		{
			return $this->started;
		}
		public function setMetadataBag(MetadataBag $bag = null)
		{
			if (null === $bag) {
				$bag = new MetadataBag();
			}
			$this->metadataBag = $bag;
		}
		public function getMetadataBag()
		{
			return $this->metadataBag;
		}
		protected function generateId()
		{
			return sha1(uniqid(mt_rand()));
		}
		protected function loadSession()
		{
			$bags = array_merge($this->bags, array($this->metadataBag));
			foreach ($bags as $bag) {
				$key = $bag->getStorageKey();
				$this->data[$key] = isset($this->data[$key]) ? $this->data[$key] : array();
				$bag->initialize($this->data[$key]);
			}
			$this->started = true;
			$this->closed = false;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage.php
	 */
	class MockFileSessionStorage extends MockArraySessionStorage
	{
		private $savePath;
		private $sessionData;
		public function __construct($savePath = null, $name = 'MOCKSESSID', MetadataBag $metaBag = null)
		{
			if (null === $savePath) {
				$savePath = sys_get_temp_dir();
			}
			if (!is_dir($savePath)) {
				mkdir($savePath, 0777, true);
			}
			$this->savePath = $savePath;
			parent::__construct($name, $metaBag);
		}
		public function start()
		{
			if ($this->started) {
				return true;
			}
			if (!$this->id) {
				$this->id = $this->generateId();
			}
			$this->read();
			$this->started = true;
			return true;
		}
		public function regenerate($destroy = false, $lifetime = null)
		{
			if (!$this->started) {
				$this->start();
			}
			if ($destroy) {
				$this->destroy();
			}
			return parent::regenerate($destroy, $lifetime);
		}
		public function save()
		{
			if (!$this->started) {
				throw new \RuntimeException("Trying to save a session that was not started yet or was already closed");
			}
			file_put_contents($this->getFilePath(), serialize($this->data));
			$this->started = false;
		}
		private function destroy()
		{
			if (is_file($this->getFilePath())) {
				unlink($this->getFilePath());
			}
		}
		private function getFilePath()
		{
			return $this->savePath . '/' . $this->id . '.mocksess';
		}
		private function read()
		{
			$filePath = $this->getFilePath();
			$this->data = is_readable($filePath) && is_file($filePath) ? unserialize(file_get_contents($filePath)) : array();
			$this->loadSession();
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage.php
	 */
	class NativeSessionStorage implements SessionStorageInterface
	{
		protected $bags;
		protected $started = false;
		protected $closed = false;
		protected $saveHandler;
		protected $metadataBag;
		public function __construct(array $options = array(), $handler = null, MetadataBag $metaBag = null)
		{
			ini_set('session.cache_limiter', '');
			ini_set('session.use_cookies', 1);
			if (version_compare(phpversion(), '5.4.0', '>=')) {
				session_register_shutdown();
			} else {
				register_shutdown_function('session_write_close');
			}
			$this->setMetadataBag($metaBag);
			$this->setOptions($options);
			$this->setSaveHandler($handler);
		}
		public function getSaveHandler()
		{
			return $this->saveHandler;
		}
		public function start()
		{
			if ($this->started && !$this->closed) {
				return true;
			}
			if (!$this->started && !$this->closed && $this->saveHandler->isActive() && $this->saveHandler->isSessionHandlerInterface()) {
				$this->loadSession();
				return true;
			}
			if (ini_get('session.use_cookies') && headers_sent()) {
				throw new \RuntimeException('Failed to start the session because headers have already been sent.');
			}
			if (!session_start()) {
				throw new \RuntimeException('Failed to start the session');
			}
			$this->loadSession();
			if (!$this->saveHandler->isWrapper() && !$this->saveHandler->isSessionHandlerInterface()) {
				$this->saveHandler->setActive(false);
			}
			return true;
		}
		public function getId()
		{
			if (!$this->started) {
				return '';
			}
			return $this->saveHandler->getId();
		}
		public function setId($id)
		{
			$this->saveHandler->setId($id);
		}
		public function getName()
		{
			return $this->saveHandler->getName();
		}
		public function setName($name)
		{
			$this->saveHandler->setName($name);
		}
		public function regenerate($destroy = false, $lifetime = null)
		{
			if (null !== $lifetime) {
				ini_set('session.cookie_lifetime', $lifetime);
			}
			if ($destroy) {
				$this->metadataBag->stampNew();
			}
			return session_regenerate_id($destroy);
		}
		public function save()
		{
			session_write_close();
			if (!$this->saveHandler->isWrapper() && !$this->getSaveHandler()->isSessionHandlerInterface()) {
				$this->saveHandler->setActive(false);
			}
			$this->closed = true;
		}
		public function clear()
		{
			foreach ($this->bags as $bag) {
				$bag->clear();
			}
			$_SESSION = array();
			$this->loadSession();
		}
		public function registerBag(SessionBagInterface $bag)
		{
			$this->bags[$bag->getName() ] = $bag;
		}
		public function getBag($name)
		{
			if (!isset($this->bags[$name])) {
				throw new \InvalidArgumentException(sprintf('The SessionBagInterface %s is not registered.', $name));
			}
			if ($this->saveHandler->isActive() && !$this->started) {
				$this->loadSession();
			} elseif (!$this->started) {
				$this->start();
			}
			return $this->bags[$name];
		}
		public function setMetadataBag(MetadataBag $metaBag = null)
		{
			if (null === $metaBag) {
				$metaBag = new MetadataBag();
			}
			$this->metadataBag = $metaBag;
		}
		public function getMetadataBag()
		{
			return $this->metadataBag;
		}
		public function isStarted()
		{
			return $this->started;
		}
		public function setOptions(array $options)
		{
			$validOptions = array_flip(array('cache_limiter', 'cookie_domain', 'cookie_httponly', 'cookie_lifetime', 'cookie_path', 'cookie_secure', 'entropy_file', 'entropy_length', 'gc_divisor', 'gc_maxlifetime', 'gc_probability', 'hash_bits_per_character', 'hash_function', 'name', 'referer_check', 'serialize_handler', 'use_cookies', 'use_only_cookies', 'use_trans_sid', 'upload_progress.enabled', 'upload_progress.cleanup', 'upload_progress.prefix', 'upload_progress.name', 'upload_progress.freq', 'upload_progress.min-freq', 'url_rewriter.tags',));
			foreach ($options as $key => $value) {
				if (isset($validOptions[$key])) {
					ini_set('session.' . $key, $value);
				}
			}
		}
		public function setSaveHandler($saveHandler = null)
		{
			if (!$saveHandler instanceof AbstractProxy && $saveHandler instanceof \SessionHandlerInterface) {
				$saveHandler = new SessionHandlerProxy($saveHandler);
			} elseif (!$saveHandler instanceof AbstractProxy) {
				$saveHandler = new NativeProxy();
			}
			$this->saveHandler = $saveHandler;
			if ($this->saveHandler instanceof \SessionHandlerInterface) {
				if (version_compare(phpversion(), '5.4.0', '>=')) {
					session_set_save_handler($this->saveHandler, false);
				} else {
					session_set_save_handler(array($this->saveHandler, 'open'), array($this->saveHandler, 'close'), array($this->saveHandler, 'read'), array($this->saveHandler, 'write'), array($this->saveHandler, 'destroy'), array($this->saveHandler, 'gc'));
				}
			}
		}
		protected function loadSession(array & $session = null)
		{
			if (null === $session) {
				$session = & $_SESSION;
			}
			$bags = array_merge($this->bags, array($this->metadataBag));
			foreach ($bags as $bag) {
				$key = $bag->getStorageKey();
				$session[$key] = isset($session[$key]) ? $session[$key] : array();
				$bag->initialize($session[$key]);
			}
			$this->started = true;
			$this->closed = false;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface.php
	 */
	interface SessionStorageInterface
	{
		public function start();
		public function isStarted();
		public function getId();
		public function setId($id);
		public function getName();
		public function setName($name);
		public function regenerate($destroy = false, $lifetime = null);
		public function save();
		public function clear();
		public function getBag($name);
		public function registerBag(SessionBagInterface $bag);
		public function getMetadataBag();
	}
}
namespace Laravel\Database\Eloquent
{
	use Laravel\Database;
	use Laravel\Database\Eloquent\Relationships\Has_Many_And_Belongs_To;
	use Laravel\Str;
	use Laravel\Event;
	/**
	 * laravel\database\eloquent\model.php
	 */
	abstract class Model
	{
		public $attributes = array();
		public $original = array();
		public $relationships = array();
		public $exists = false;
		public $includes = array();
		public static $key = 'id';
		public static $accessible;
		public static $hidden = array();
		public static $timestamps = true;
		public static $table;
		public static $connection;
		public static $sequence;
		public static $per_page = 20;
		public function __construct($attributes = array(), $exists = false)
		{
			$this->exists = $exists;
			$this->fill($attributes);
		}
		public function fill(array $attributes, $raw = false)
		{
			foreach ($attributes as $key => $value) {
				if ($raw) {
					$this->set_attribute($key, $value);
					continue;
				}
				if (is_array(static ::$accessible)) {
					if (in_array($key, static ::$accessible)) {
						$this->$key = $value;
					}
				} else {
					$this->$key = $value;
				}
			}
			if (count($this->original) === 0) {
				$this->original = $this->attributes;
			}
			return $this;
		}
		public function fill_raw(array $attributes)
		{
			return $this->fill($attributes, true);
		}
		public static function accessible($attributes = null)
		{
			if (is_null($attributes)) return static ::$accessible;
			static ::$accessible = $attributes;
		}
		public static function create($attributes)
		{
			$model = new static ($attributes);
			$success = $model->save();
			return ($success) ? $model : false;
		}
		public static function update($id, $attributes)
		{
			$model = new static (array(), true);
			$model->fill($attributes);
			if (static ::$timestamps) $model->timestamp();
			return $model->query()->where($model->key(), '=', $id)->update($model->attributes);
		}
		public static function all()
		{
			return with(new static)->query()->get();
		}
		public function _with($includes)
		{
			$this->includes = (array)$includes;
			return $this;
		}
		public function has_one($model, $foreign = null)
		{
			return $this->has_one_or_many(__FUNCTION__, $model, $foreign);
		}
		public function has_many($model, $foreign = null)
		{
			return $this->has_one_or_many(__FUNCTION__, $model, $foreign);
		}
		protected function has_one_or_many($type, $model, $foreign)
		{
			if ($type == 'has_one') {
				return new Relationships\Has_One($this, $model, $foreign);
			} else {
				return new Relationships\Has_Many($this, $model, $foreign);
			}
		}
		public function belongs_to($model, $foreign = null)
		{
			if (is_null($foreign)) {
				list(, $caller) = debug_backtrace(false);
				$foreign = "{$caller['function']}_id";
			}
			return new Relationships\Belongs_To($this, $model, $foreign);
		}
		public function has_many_and_belongs_to($model, $table = null, $foreign = null, $other = null)
		{
			return new Has_Many_And_Belongs_To($this, $model, $table, $foreign, $other);
		}
		public function push()
		{
			$this->save();
			foreach ($this->relationships as $name => $models) {
				if (!is_array($models)) {
					$models = array($models);
				}
				foreach ($models as $model) {
					$model->push();
				}
			}
		}
		public function save()
		{
			if (!$this->dirty()) return true;
			if (static ::$timestamps) {
				$this->timestamp();
			}
			$this->fire_event('saving');
			if ($this->exists) {
				$query = $this->query()->where(static ::$key, '=', $this->get_key());
				$result = $query->update($this->get_dirty()) === 1;
				if ($result) $this->fire_event('updated');
			} else {
				$id = $this->query()->insert_get_id($this->attributes, $this->key());
				$this->set_key($id);
				$this->exists = $result = is_numeric($this->get_key());
				if ($result) $this->fire_event('created');
			}
			$this->original = $this->attributes;
			if ($result) {
				$this->fire_event('saved');
			}
			return $result;
		}
		public function delete()
		{
			if ($this->exists) {
				$this->fire_event('deleting');
				$result = $this->query()->where(static ::$key, '=', $this->get_key())->delete();
				$this->fire_event('deleted');
				return $result;
			}
		}
		public function timestamp()
		{
			$this->updated_at = new \DateTime;
			if (!$this->exists) $this->created_at = $this->updated_at;
		}
		public function touch()
		{
			$this->timestamp();
			$this->save();
		}
		protected function _query()
		{
			return new Query($this);
		}
		final public function sync()
		{
			$this->original = $this->attributes;
			return true;
		}
		public function changed($attribute)
		{
			return array_get($this->attributes, $attribute) != array_get($this->original, $attribute);
		}
		public function dirty()
		{
			return !$this->exists or count($this->get_dirty()) > 0;
		}
		public function table()
		{
			return static ::$table ? : strtolower(Str::plural(class_basename($this)));
		}
		public function get_dirty()
		{
			$dirty = array();
			foreach ($this->attributes as $key => $value) {
				if (!array_key_exists($key, $this->original) or $value != $this->original[$key]) {
					$dirty[$key] = $value;
				}
			}
			return $dirty;
		}
		public function get_key()
		{
			return array_get($this->attributes, static ::$key);
		}
		public function set_key($value)
		{
			return $this->set_attribute(static ::$key, $value);
		}
		public function get_attribute($key)
		{
			return array_get($this->attributes, $key);
		}
		public function set_attribute($key, $value)
		{
			$this->attributes[$key] = $value;
		}
		final public function purge($key)
		{
			unset($this->original[$key]);
			unset($this->attributes[$key]);
		}
		public function to_array()
		{
			$attributes = array();
			foreach (array_keys($this->attributes) as $attribute) {
				if (!in_array($attribute, static ::$hidden)) {
					$attributes[$attribute] = $this->$attribute;
				}
			}
			foreach ($this->relationships as $name => $models) {
				if (in_array($name, static ::$hidden)) continue;
				if ($models instanceof Model) {
					$attributes[$name] = $models->to_array();
				} elseif (is_array($models)) {
					$attributes[$name] = array();
					foreach ($models as $id => $model) {
						$attributes[$name][$id] = $model->to_array();
					}
				} elseif (is_null($models)) {
					$attributes[$name] = $models;
				}
			}
			return $attributes;
		}
		protected function fire_event($event)
		{
			$events = array("eloquent.{$event}", "eloquent.{$event}: " . get_class($this));
			Event::fire($events, array($this));
		}
		public function __get($key)
		{
			if (array_key_exists($key, $this->relationships)) {
				return $this->relationships[$key];
			} elseif (array_key_exists($key, $this->attributes)) {
				return $this->{"get_{$key}"
			}
			();
		} elseif (method_exists($this, $key)) {
			return $this->relationships[$key] = $this->$key()->results();
		} else {
			return $this->{"get_{$key}"
		}
		();
	}
}
public function __set($key, $value)
{
	$this->{"set_{$key}"
}
($value);
}
public function __isset($key)
{
	foreach (array('attributes', 'relationships') as $source) {
		if (array_key_exists($key, $this->{$source})) return !empty($this->{$source}[$key]);
	}
	return false;
}
public function __unset($key)
{
	foreach (array('attributes', 'relationships') as $source) {
		unset($this->{$source}[$key]);
	}
}
public function __call($method, $parameters)
{
	$meta = array('key', 'table', 'connection', 'sequence', 'per_page', 'timestamps');
	if (in_array($method, $meta)) {
		return static ::$$method;
	}
	$underscored = array('with', 'query');
	if (in_array($method, $underscored)) {
		return call_user_func_array(array($this, '_' . $method), $parameters);
	}
	if (starts_with($method, 'get_')) {
		return $this->get_attribute(substr($method, 4));
	} elseif (starts_with($method, 'set_')) {
		$this->set_attribute(substr($method, 4), $parameters[0]);
	} else {
		return call_user_func_array(array($this->query(), $method), $parameters);
	}
}
public static function __callStatic($method, $parameters)
{
	$model = get_called_class();
	return call_user_func_array(array(new $model, $method), $parameters);
}
}
/**
 * laravel\database\eloquent\pivot.php
 */
class Pivot extends Model
{
	protected $pivot_table;
	protected $pivot_connection;
	public static $timestamps = true;
	public function __construct($table, $connection = null)
	{
		$this->pivot_table = $table;
		$this->pivot_connection = $connection;
		parent::__construct(array(), true);
	}
	public function table()
	{
		return $this->pivot_table;
	}
	public function connection()
	{
		return $this->pivot_connection;
	}
}
/**
 * laravel\database\eloquent\query.php
 */
class Query
{
	public $model;
	public $table;
	public $includes = array();
	public $passthru = array('lists', 'only', 'insert', 'insert_get_id', 'update', 'increment', 'delete', 'decrement', 'count', 'min', 'max', 'avg', 'sum',);
	public function __construct($model)
	{
		$this->model = ($model instanceof Model) ? $model : new $model;
		$this->table = $this->table();
	}
	public function find($id, $columns = array('*'))
	{
		$model = $this->model;
		$this->table->where($model::$key, '=', $id);
		return $this->first($columns);
	}
	public function first($columns = array('*'))
	{
		$results = $this->hydrate($this->model, $this->table->take(1)->get($columns));
		return (count($results) > 0) ? head($results) : null;
	}
	public function get($columns = array('*'))
	{
		return $this->hydrate($this->model, $this->table->get($columns));
	}
	public function paginate($per_page = null, $columns = array('*'))
	{
		$per_page = $per_page ? : $this->model->per_page();
		$paginator = $this->table->paginate($per_page, $columns);
		$paginator->results = $this->hydrate($this->model, $paginator->results);
		return $paginator;
	}
	public function hydrate($model, $results)
	{
		$class = get_class($model);
		$models = array();
		foreach ((array)$results as $result) {
			$result = (array)$result;
			$new = new $class(array(), true);
			$new->fill_raw($result);
			$models[] = $new;
		}
		if (count($results) > 0) {
			foreach ($this->model_includes() as $relationship => $constraints) {
				if (str_contains($relationship, '.')) {
					continue;
				}
				$this->load($models, $relationship, $constraints);
			}
		}
		if ($this instanceof Relationships\Has_Many_And_Belongs_To) {
			$this->hydrate_pivot($models);
		}
		return $models;
	}
	protected function load(&$results, $relationship, $constraints)
	{
		$query = $this->model->$relationship();
		$query->model->includes = $this->nested_includes($relationship);
		$query->table->reset_where();
		$query->eagerly_constrain($results);
		if (!is_null($constraints)) {
			$query->table->where_nested($constraints);
		}
		$query->initialize($results, $relationship);
		$query->match($relationship, $results, $query->get());
	}
	protected function nested_includes($relationship)
	{
		$nested = array();
		foreach ($this->model_includes() as $include => $constraints) {
			if (starts_with($include, $relationship . '.')) {
				$nested[substr($include, strlen($relationship . '.')) ] = $constraints;
			}
		}
		return $nested;
	}
	protected function model_includes()
	{
		$includes = array();
		foreach ($this->model->includes as $relationship => $constraints) {
			if (is_numeric($relationship)) {
				list($relationship, $constraints) = array($constraints, null);
			}
			$includes[$relationship] = $constraints;
		}
		return $includes;
	}
	protected function table()
	{
		return $this->connection()->table($this->model->table());
	}
	public function connection()
	{
		return Database::connection($this->model->connection());
	}
	public function __call($method, $parameters)
	{
		$result = call_user_func_array(array($this->table, $method), $parameters);
		if (in_array($method, $this->passthru)) {
			return $result;
		}
		return $this;
	}
}
}
namespace Laravel\Cache\Drivers
{
	use Laravel\Config;
	use Laravel\Database as DB;
	use Laravel\Database\Connection;
	/**
	 * laravel\cache\drivers\apc.php
	 */
	class APC extends Driver
	{
		protected $key;
		public function __construct($key)
		{
			$this->key = $key;
		}
		public function has($key)
		{
			return (!is_null($this->get($key)));
		}
		protected function retrieve($key)
		{
			if (($cache = apc_fetch($this->key . $key)) !== false) {
				return $cache;
			}
		}
		public function put($key, $value, $minutes)
		{
			apc_store($this->key . $key, $value, $minutes * 60);
		}
		public function forever($key, $value)
		{
			return $this->put($key, $value, 0);
		}
		public function forget($key)
		{
			apc_delete($this->key . $key);
		}
	}
	/**
	 * laravel\cache\drivers\database.php
	 */
	class Database extends Driver
	{
		protected $key;
		public function __construct($key)
		{
			$this->key = $key;
		}
		public function has($key)
		{
			return (!is_null($this->get($key)));
		}
		protected function retrieve($key)
		{
			$cache = $this->table()->where('key', '=', $this->key . $key)->first();
			if (!is_null($cache)) {
				if (time() >= $cache->expiration) return $this->forget($key);
				return unserialize($cache->value);
			}
		}
		public function put($key, $value, $minutes)
		{
			$key = $this->key . $key;
			$value = serialize($value);
			$expiration = $this->expiration($minutes);
			try {
				$this->table()->insert(compact('key', 'value', 'expiration'));
			}
			catch(\Exception $e) {
				$this->table()->where('key', '=', $key)->update(compact('value', 'expiration'));
			}
		}
		public function forever($key, $value)
		{
			return $this->put($key, $value, 2628000);
		}
		public function forget($key)
		{
			$this->table()->where('key', '=', $this->key . $key)->delete();
		}
		protected function table()
		{
			$connection = DB::connection(Config::get('cache.database.connection'));
			return $connection->table(Config::get('cache.database.table'));
		}
	}
	/**
	 * laravel\cache\drivers\driver.php
	 */
	abstract class Driver
	{
		abstract public function has($key);
		public function get($key, $default = null)
		{
			return (!is_null($item = $this->retrieve($key))) ? $item : value($default);
		}
		abstract protected function retrieve($key);
		abstract public function put($key, $value, $minutes);
		public function remember($key, $default, $minutes, $function = 'put')
		{
			if (!is_null($item = $this->get($key, null))) return $item;
			$this->$function($key, $default = value($default), $minutes);
			return $default;
		}
		public function sear($key, $default)
		{
			return $this->remember($key, $default, null, 'forever');
		}
		abstract public function forget($key);
		protected function expiration($minutes)
		{
			return time() + ($minutes * 60);
		}
	}
	/**
	 * laravel\cache\drivers\file.php
	 */
	class File extends Driver
	{
		protected $path;
		public function __construct($path)
		{
			$this->path = $path;
		}
		public function has($key)
		{
			return (!is_null($this->get($key)));
		}
		protected function retrieve($key)
		{
			if (!file_exists($this->path . $key)) return null;
			if (time() >= substr($cache = file_get_contents($this->path . $key), 0, 10)) {
				return $this->forget($key);
			}
			return unserialize(substr($cache, 10));
		}
		public function put($key, $value, $minutes)
		{
			if ($minutes <= 0) return;
			$value = $this->expiration($minutes) . serialize($value);
			file_put_contents($this->path . $key, $value, LOCK_EX);
		}
		public function forever($key, $value)
		{
			return $this->put($key, $value, 2628000);
		}
		public function forget($key)
		{
			if (file_exists($this->path . $key)) @unlink($this->path . $key);
		}
	}
	/**
	 * laravel\cache\drivers\memcached.php
	 */
	class Memcached extends Sectionable
	{
		public $memcache;
		protected $key;
		public function __construct(\Memcached $memcache, $key)
		{
			$this->key = $key;
			$this->memcache = $memcache;
		}
		public function has($key)
		{
			return (!is_null($this->get($key)));
		}
		protected function retrieve($key)
		{
			if ($this->sectionable($key)) {
				list($section, $key) = $this->parse($key);
				return $this->get_from_section($section, $key);
			} elseif (($cache = $this->memcache->get($this->key . $key)) !== false) {
				return $cache;
			}
		}
		public function put($key, $value, $minutes)
		{
			if ($this->sectionable($key)) {
				list($section, $key) = $this->parse($key);
				return $this->put_in_section($section, $key, $value, $minutes);
			} else {
				$this->memcache->set($this->key . $key, $value, $minutes * 60);
			}
		}
		public function forever($key, $value)
		{
			if ($this->sectionable($key)) {
				list($section, $key) = $this->parse($key);
				return $this->forever_in_section($section, $key, $value);
			} else {
				return $this->put($key, $value, 0);
			}
		}
		public function forget($key)
		{
			if ($this->sectionable($key)) {
				list($section, $key) = $this->parse($key);
				if ($key == '*') {
					$this->forget_section($section);
				} else {
					$this->forget_in_section($section, $key);
				}
			} else {
				$this->memcache->delete($this->key . $key);
			}
		}
		public function forget_section($section)
		{
			return $this->memcache->increment($this->key . $this->section_key($section));
		}
		protected function section_id($section)
		{
			return $this->sear($this->section_key($section), function ()
			{
				return rand(1, 10000);
			});
		}
		protected function section_key($section)
		{
			return $section . '_section_key';
		}
		protected function section_item_key($section, $key)
		{
			return $section . '#' . $this->section_id($section) . '#' . $key;
		}
	}
	/**
	 * laravel\cache\drivers\memory.php
	 */
	class Memory extends Sectionable
	{
		public $storage = array();
		public function has($key)
		{
			return (!is_null($this->get($key)));
		}
		protected function retrieve($key)
		{
			if ($this->sectionable($key)) {
				list($section, $key) = $this->parse($key);
				return $this->get_from_section($section, $key);
			} else {
				return array_get($this->storage, $key);
			}
		}
		public function put($key, $value, $minutes)
		{
			if ($this->sectionable($key)) {
				list($section, $key) = $this->parse($key);
				return $this->put_in_section($section, $key, $value, $minutes);
			} else {
				array_set($this->storage, $key, $value);
			}
		}
		public function forever($key, $value)
		{
			if ($this->sectionable($key)) {
				list($section, $key) = $this->parse($key);
				return $this->forever_in_section($section, $key, $value);
			} else {
				$this->put($key, $value, 0);
			}
		}
		public function forget($key)
		{
			if ($this->sectionable($key)) {
				list($section, $key) = $this->parse($key);
				if ($key == '*') {
					$this->forget_section($section);
				} else {
					$this->forget_in_section($section, $key);
				}
			} else {
				array_forget($this->storage, $key);
			}
		}
		public function forget_section($section)
		{
			array_forget($this->storage, 'section#' . $section);
		}
		public function flush()
		{
			$this->storage = array();
		}
		protected function section_item_key($section, $key)
		{
			return "section#{$section}.{$key}";
		}
	}
	/**
	 * laravel\cache\drivers\redis.php
	 */
	class Redis extends Driver
	{
		protected $redis;
		public function __construct(\Laravel\Redis $redis)
		{
			$this->redis = $redis;
		}
		public function has($key)
		{
			return (!is_null($this->redis->get($key)));
		}
		protected function retrieve($key)
		{
			if (!is_null($cache = $this->redis->get($key))) {
				return unserialize($cache);
			}
		}
		public function put($key, $value, $minutes)
		{
			$this->forever($key, $value);
			$this->redis->expire($key, $minutes * 60);
		}
		public function forever($key, $value)
		{
			$this->redis->set($key, serialize($value));
		}
		public function forget($key)
		{
			$this->redis->del($key);
		}
	}
	/**
	 * laravel\cache\drivers\sectionable.php
	 */
	abstract class Sectionable extends Driver
	{
		public $implicit = true;
		public $delimiter = '::';
		public function get_from_section($section, $key, $default = null)
		{
			return $this->get($this->section_item_key($section, $key), $default);
		}
		public function put_in_section($section, $key, $value, $minutes)
		{
			$this->put($this->section_item_key($section, $key), $value, $minutes);
		}
		public function forever_in_section($section, $key, $value)
		{
			return $this->forever($this->section_item_key($section, $key), $value);
		}
		public function remember_in_section($section, $key, $default, $minutes, $function = 'put')
		{
			$key = $this->section_item_key($section, $key);
			return $this->remember($key, $default, $minutes, $function);
		}
		public function sear_in_section($section, $key, $default)
		{
			return $this->sear($this->section_item_key($section, $key), $default);
		}
		public function forget_in_section($section, $key)
		{
			return $this->forget($this->section_item_key($section, $key));
		}
		abstract public function forget_section($section);
		protected function sectionable($key)
		{
			return $this->implicit and $this->sectioned($key);
		}
		protected function sectioned($key)
		{
			return str_contains($key, '::');
		}
		protected function parse($key)
		{
			return explode('::', $key, 2);
		}
	}
	/**
	 * laravel\cache\drivers\wincache.php
	 */
	class WinCache extends Driver
	{
		protected $key;
		public function __construct($key)
		{
			$this->key = $key;
		}
		public function has($key)
		{
			return (!is_null($this->get($key)));
		}
		protected function retrieve($key)
		{
			if (($cache = wincache_ucache_get($this->key . $key)) !== false) {
				return $cache;
			}
		}
		public function put($key, $value, $minutes)
		{
			wincache_ucache_add($this->key . $key, $value, $minutes * 60);
		}
		public function forever($key, $value)
		{
			return $this->put($key, $value, 0);
		}
		public function forget($key)
		{
			wincache_ucache_delete($this->key . $key);
		}
	}
}
namespace Symfony\Component\HttpFoundation\File
{
	use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
	use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
	use Symfony\Component\HttpFoundation\File\Exception\FileException;
	use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\File.php
	 */
	class File extends \SplFileInfo
	{
		public function __construct($path, $checkPath = true)
		{
			if ($checkPath && !is_file($path)) {
				throw new FileNotFoundException($path);
			}
			parent::__construct($path);
		}
		public function guessExtension()
		{
			$type = $this->getMimeType();
			$guesser = ExtensionGuesser::getInstance();
			return $guesser->guess($type);
		}
		public function getMimeType()
		{
			$guesser = MimeTypeGuesser::getInstance();
			return $guesser->guess($this->getPathname());
		}
		public function getExtension()
		{
			return pathinfo($this->getBasename(), PATHINFO_EXTENSION);
		}
		public function move($directory, $name = null)
		{
			$target = $this->getTargetFile($directory, $name);
			if (!@rename($this->getPathname(), $target)) {
				$error = error_get_last();
				throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error['message'])));
			}
			@chmod($target, 0666 & ~umask());
			return $target;
		}
		protected function getTargetFile($directory, $name = null)
		{
			if (!is_dir($directory)) {
				if (false === @mkdir($directory, 0777, true)) {
					throw new FileException(sprintf('Unable to create the "%s" directory', $directory));
				}
			} elseif (!is_writable($directory)) {
				throw new FileException(sprintf('Unable to write in the "%s" directory', $directory));
			}
			$target = $directory . DIRECTORY_SEPARATOR . (null === $name ? $this->getBasename() : $this->getName($name));
			return new File($target, false);
		}
		protected function getName($name)
		{
			$originalName = str_replace('\\', '/', $name);
			$pos = strrpos($originalName, '/');
			$originalName = false === $pos ? $originalName : substr($originalName, $pos + 1);
			return $originalName;
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\File\UploadedFile.php
	 */
	class UploadedFile extends File
	{
		private $test = false;
		private $originalName;
		private $mimeType;
		private $size;
		private $error;
		public function __construct($path, $originalName, $mimeType = null, $size = null, $error = null, $test = false)
		{
			if (!ini_get('file_uploads')) {
				throw new FileException(sprintf('Unable to create UploadedFile because "file_uploads" is disabled in your php.ini file (%s)', get_cfg_var('cfg_file_path')));
			}
			$this->originalName = $this->getName($originalName);
			$this->mimeType = $mimeType ? : 'application/octet-stream';
			$this->size = $size;
			$this->error = $error ? : UPLOAD_ERR_OK;
			$this->test = (Boolean)$test;
			parent::__construct($path, UPLOAD_ERR_OK === $this->error);
		}
		public function getClientOriginalName()
		{
			return $this->originalName;
		}
		public function getClientMimeType()
		{
			return $this->mimeType;
		}
		public function getClientSize()
		{
			return $this->size;
		}
		public function getError()
		{
			return $this->error;
		}
		public function isValid()
		{
			return $this->error === UPLOAD_ERR_OK;
		}
		public function move($directory, $name = null)
		{
			if ($this->isValid()) {
				if ($this->test) {
					return parent::move($directory, $name);
				} elseif (is_uploaded_file($this->getPathname())) {
					$target = $this->getTargetFile($directory, $name);
					if (!@move_uploaded_file($this->getPathname(), $target)) {
						$error = error_get_last();
						throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error['message'])));
					}
					@chmod($target, 0666 & ~umask());
					return $target;
				}
			}
			throw new FileException(sprintf('The file "%s" has not been uploaded via Http', $this->getPathname()));
		}
		public static function getMaxFilesize()
		{
			$max = trim(ini_get('upload_max_filesize'));
			if ('' === $max) {
				return PHP_INT_MAX;
			}
			switch (strtolower(substr($max, -1))) {
			case 'g':
				$max*= 1024;
			case 'm':
				$max*= 1024;
			case 'k':
				$max*= 1024;
			}
			return (integer)$max;
		}
	}
}
namespace Symfony\Component\HttpFoundation\Session
{
	use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
	use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
	use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
	use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
	use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
	use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
	use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
	use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\Session.php
	 */
	class Session implements SessionInterface, \IteratorAggregate, \Countable
	{
		protected $storage;
		private $flashName;
		private $attributeName;
		public function __construct(SessionStorageInterface $storage = null, AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null)
		{
			$this->storage = $storage ? : new NativeSessionStorage();
			$attributes = $attributes ? : new AttributeBag();
			$this->attributeName = $attributes->getName();
			$this->registerBag($attributes);
			$flashes = $flashes ? : new FlashBag();
			$this->flashName = $flashes->getName();
			$this->registerBag($flashes);
		}
		public function start()
		{
			return $this->storage->start();
		}
		public function has($name)
		{
			return $this->storage->getBag($this->attributeName)->has($name);
		}
		public function get($name, $default = null)
		{
			return $this->storage->getBag($this->attributeName)->get($name, $default);
		}
		public function set($name, $value)
		{
			$this->storage->getBag($this->attributeName)->set($name, $value);
		}
		public function all()
		{
			return $this->storage->getBag($this->attributeName)->all();
		}
		public function replace(array $attributes)
		{
			$this->storage->getBag($this->attributeName)->replace($attributes);
		}
		public function remove($name)
		{
			return $this->storage->getBag($this->attributeName)->remove($name);
		}
		public function clear()
		{
			$this->storage->getBag($this->attributeName)->clear();
		}
		public function isStarted()
		{
			return $this->storage->isStarted();
		}
		public function getIterator()
		{
			return new \ArrayIterator($this->storage->getBag($this->attributeName)->all());
		}
		public function count()
		{
			return count($this->storage->getBag($this->attributeName)->all());
		}
		public function invalidate($lifetime = null)
		{
			$this->storage->clear();
			return $this->migrate(true, $lifetime);
		}
		public function migrate($destroy = false, $lifetime = null)
		{
			return $this->storage->regenerate($destroy, $lifetime);
		}
		public function save()
		{
			$this->storage->save();
		}
		public function getId()
		{
			return $this->storage->getId();
		}
		public function setId($id)
		{
			$this->storage->setId($id);
		}
		public function getName()
		{
			return $this->storage->getName();
		}
		public function setName($name)
		{
			$this->storage->setName($name);
		}
		public function getMetadataBag()
		{
			return $this->storage->getMetadataBag();
		}
		public function registerBag(SessionBagInterface $bag)
		{
			$this->storage->registerBag($bag);
		}
		public function getBag($name)
		{
			return $this->storage->getBag($name);
		}
		public function getFlashBag()
		{
			return $this->getBag($this->flashName);
		}
		public function getFlashes()
		{
			$all = $this->getBag($this->flashName)->all();
			$return = array();
			if ($all) {
				foreach ($all as $name => $array) {
					if (is_numeric(key($array))) {
						$return[$name] = reset($array);
					} else {
						$return[$name] = $array;
					}
				}
			}
			return $return;
		}
		public function setFlashes($values)
		{
			foreach ($values as $name => $value) {
				$this->getBag($this->flashName)->set($name, $value);
			}
		}
		public function getFlash($name, $default = null)
		{
			$return = $this->getBag($this->flashName)->get($name);
			return empty($return) ? $default : reset($return);
		}
		public function setFlash($name, $value)
		{
			$this->getBag($this->flashName)->set($name, $value);
		}
		public function hasFlash($name)
		{
			return $this->getBag($this->flashName)->has($name);
		}
		public function removeFlash($name)
		{
			$this->getBag($this->flashName)->get($name);
		}
		public function clearFlashes()
		{
			return $this->getBag($this->flashName)->clear();
		}
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\SessionBagInterface.php
	 */
	interface SessionBagInterface
	{
		public function getName();
		public function initialize(array & $array);
		public function getStorageKey();
		public function clear();
	}
	/**
	 * laravel\vendor\Symfony\Component\HttpFoundation\Session\SessionInterface.php
	 */
	interface SessionInterface
	{
		public function start();
		public function getId();
		public function setId($id);
		public function getName();
		public function setName($name);
		public function invalidate($lifetime = null);
		public function migrate($destroy = false, $lifetime = null);
		public function save();
		public function has($name);
		public function get($name, $default = null);
		public function set($name, $value);
		public function all();
		public function replace(array $attributes);
		public function remove($name);
		public function clear();
		public function isStarted();
		public function registerBag(SessionBagInterface $bag);
		public function getBag($name);
		public function getMetadataBag();
	}
}
namespace Laravel\Database\Query\Grammars
{
	use Laravel\Database\Query;
	use Laravel\Database\Expression;
	/**
	 * laravel\database\query\grammars\grammar.php
	 */
	class Grammar extends \Laravel\Database\Grammar
	{
		public $datetime = 'Y-m-d H:i:s';
		protected $components = array('aggregate', 'selects', 'from', 'joins', 'wheres', 'groupings', 'havings', 'orderings', 'limit', 'offset',);
		public function select(Query $query)
		{
			return $this->concatenate($this->components($query));
		}
		final protected function components($query)
		{
			foreach ($this->components as $component) {
				if (!is_null($query->$component)) {
					$sql[$component] = call_user_func(array($this, $component), $query);
				}
			}
			return (array)$sql;
		}
		final protected function concatenate($components)
		{
			return implode(' ', array_filter($components, function ($value)
			{
				return (string)$value !== '';
			}));
		}
		protected function selects(Query $query)
		{
			if (!is_null($query->aggregate)) return;
			$select = ($query->distinct) ? 'SELECT DISTINCT ' : 'SELECT ';
			return $select . $this->columnize($query->selects);
		}
		protected function aggregate(Query $query)
		{
			$column = $this->columnize($query->aggregate['columns']);
			if ($query->distinct and $column !== '*') {
				$column = 'DISTINCT ' . $column;
			}
			return 'SELECT ' . $query->aggregate['aggregator'] . '(' . $column . ') AS ' . $this->wrap('aggregate');
		}
		protected function from(Query $query)
		{
			return 'FROM ' . $this->wrap_table($query->from);
		}
		protected function joins(Query $query)
		{
			foreach ($query->joins as $join) {
				$table = $this->wrap_table($join->table);
				$clauses = array();
				foreach ($join->clauses as $clause) {
					extract($clause);
					$column1 = $this->wrap($column1);
					$column2 = $this->wrap($column2);
					$clauses[] = "{$connector} {$column1} {$operator} {$column2}";
				}
				$search = array('AND ', 'OR ');
				$clauses[0] = str_replace($search, '', $clauses[0]);
				$clauses = implode(' ', $clauses);
				$sql[] = "{$join->type} JOIN {$table} ON {$clauses}";
			}
			return implode(' ', $sql);
		}
		final protected function wheres(Query $query)
		{
			if (is_null($query->wheres)) return '';
			foreach ($query->wheres as $where) {
				$sql[] = $where['connector'] . ' ' . $this->{$where['type']}($where);
			}
			if (isset($sql)) {
				return 'WHERE ' . preg_replace('/AND |OR /', '', implode(' ', $sql), 1);
			}
		}
		protected function where_nested($where)
		{
			return '(' . substr($this->wheres($where['query']), 6) . ')';
		}
		protected function where($where)
		{
			$parameter = $this->parameter($where['value']);
			return $this->wrap($where['column']) . ' ' . $where['operator'] . ' ' . $parameter;
		}
		protected function where_in($where)
		{
			$parameters = $this->parameterize($where['values']);
			return $this->wrap($where['column']) . ' IN (' . $parameters . ')';
		}
		protected function where_not_in($where)
		{
			$parameters = $this->parameterize($where['values']);
			return $this->wrap($where['column']) . ' NOT IN (' . $parameters . ')';
		}
		protected function where_between($where)
		{
			$min = $this->parameter($where['min']);
			$max = $this->parameter($where['max']);
			return $this->wrap($where['column']) . ' BETWEEN ' . $min . ' AND ' . $max;
		}
		protected function where_not_between($where)
		{
			$min = $this->parameter($where['min']);
			$max = $this->parameter($where['max']);
			return $this->wrap($where['column']) . ' NOT BETWEEN ' . $min . ' AND ' . $max;
		}
		protected function where_null($where)
		{
			return $this->wrap($where['column']) . ' IS NULL';
		}
		protected function where_not_null($where)
		{
			return $this->wrap($where['column']) . ' IS NOT NULL';
		}
		final protected function where_raw($where)
		{
			return $where['sql'];
		}
		protected function groupings(Query $query)
		{
			return 'GROUP BY ' . $this->columnize($query->groupings);
		}
		protected function havings(Query $query)
		{
			if (is_null($query->havings)) return '';
			foreach ($query->havings as $having) {
				$sql[] = 'AND ' . $this->wrap($having['column']) . ' ' . $having['operator'] . ' ' . $this->parameter($having['value']);
			}
			return 'HAVING ' . preg_replace('/AND /', '', implode(' ', $sql), 1);
		}
		protected function orderings(Query $query)
		{
			foreach ($query->orderings as $ordering) {
				$sql[] = $this->wrap($ordering['column']) . ' ' . strtoupper($ordering['direction']);
			}
			return 'ORDER BY ' . implode(', ', $sql);
		}
		protected function limit(Query $query)
		{
			return 'LIMIT ' . $query->limit;
		}
		protected function offset(Query $query)
		{
			return 'OFFSET ' . $query->offset;
		}
		public function insert(Query $query, $values)
		{
			$table = $this->wrap_table($query->from);
			if (!is_array(reset($values))) $values = array($values);
			$columns = $this->columnize(array_keys(reset($values)));
			$parameters = $this->parameterize(reset($values));
			$parameters = implode(', ', array_fill(0, count($values), "($parameters)"));
			return "INSERT INTO {$table} ({$columns}) VALUES {$parameters}";
		}
		public function insert_get_id(Query $query, $values, $column)
		{
			return $this->insert($query, $values);
		}
		public function update(Query $query, $values)
		{
			$table = $this->wrap_table($query->from);
			foreach ($values as $column => $value) {
				$columns[] = $this->wrap($column) . ' = ' . $this->parameter($value);
			}
			$columns = implode(', ', $columns);
			return trim("UPDATE {$table} SET {$columns} " . $this->wheres($query));
		}
		public function delete(Query $query)
		{
			$table = $this->wrap_table($query->from);
			return trim("DELETE FROM {$table} " . $this->wheres($query));
		}
		public function shortcut($sql, &$bindings)
		{
			if (strpos($sql, '(...)') !== false) {
				for ($i = 0; $i < count($bindings); $i++) {
					if (is_array($bindings[$i])) {
						$parameters = $this->parameterize($bindings[$i]);
						array_splice($bindings, $i, 1, $bindings[$i]);
						$sql = preg_replace('~\(\.\.\.\)~', "({$parameters})", $sql, 1);
					}
				}
			}
			return trim($sql);
		}
	}
	/**
	 * laravel\database\query\grammars\mysql.php
	 */
	class MySQL extends Grammar
	{
		protected $wrapper = '`%s`';
	}
	/**
	 * laravel\database\query\grammars\postgres.php
	 */
	class Postgres extends Grammar
	{
		public function insert_get_id(Query $query, $values, $column)
		{
			return $this->insert($query, $values) . " RETURNING $column";
		}
	}
	/**
	 * laravel\database\query\grammars\sqlite.php
	 */
	class SQLite extends Grammar
	{
		protected function orderings(Query $query)
		{
			foreach ($query->orderings as $ordering) {
				$sql[] = $this->wrap($ordering['column']) . ' COLLATE NOCASE ' . strtoupper($ordering['direction']);
			}
			return 'ORDER BY ' . implode(', ', $sql);
		}
		public function insert(Query $query, $values)
		{
			$table = $this->wrap_table($query->from);
			if (!is_array(reset($values))) {
				$values = array($values);
			}
			if (count($values) == 1) {
				return parent::insert($query, $values[0]);
			}
			$names = $this->columnize(array_keys($values[0]));
			$columns = array();
			foreach (array_keys($values[0]) as $column) {
				$columns[] = '? AS ' . $this->wrap($column);
			}
			$columns = array_fill(9, count($values), implode(', ', $columns));
			return "INSERT INTO $table ($names) SELECT " . implode(' UNION SELECT ', $columns);
		}
	}
	/**
	 * laravel\database\query\grammars\sqlserver.php
	 */
	class SQLServer extends Grammar
	{
		protected $wrapper = '[%s]';
		public $datetime = 'Y-m-d H:i:s.000';
		public function select(Query $query)
		{
			$sql = parent::components($query);
			if ($query->offset > 0) {
				return $this->ansi_offset($query, $sql);
			}
			return $this->concatenate($sql);
		}
		protected function selects(Query $query)
		{
			if (!is_null($query->aggregate)) return;
			$select = ($query->distinct) ? 'SELECT DISTINCT ' : 'SELECT ';
			if ($query->limit > 0 and $query->offset <= 0) {
				$select.= 'TOP ' . $query->limit . ' ';
			}
			return $select . $this->columnize($query->selects);
		}
		protected function ansi_offset(Query $query, $components)
		{
			if (!isset($components['orderings'])) {
				$components['orderings'] = 'ORDER BY (SELECT 0)';
			}
			$orderings = $components['orderings'];
			$components['selects'].= ", ROW_NUMBER() OVER ({$orderings}) AS RowNum";
			unset($components['orderings']);
			$start = $query->offset + 1;
			if ($query->limit > 0) {
				$finish = $query->offset + $query->limit;
				$constraint = "BETWEEN {$start} AND {$finish}";
			} else {
				$constraint = ">= {$start}";
			}
			$sql = $this->concatenate($components);
			return "SELECT * FROM ($sql) AS TempTable WHERE RowNum {$constraint}";
		}
		protected function limit(Query $query)
		{
			return '';
		}
		protected function offset(Query $query)
		{
			return '';
		}
	}
}
