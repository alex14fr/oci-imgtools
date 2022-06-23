<?php
function hashF($f) {
	return hash_file("sha256",$f);
}

function compactJSON($x) {
	$out="";
	$inquote=false;
	for($i=0;$i<strlen($x);$i++) {
		if($inquote || ($x[$i]!="\t" && $x[$i]!="\r" && $x[$i]!="\n" && $x[$i]!=" ")) { 
			$out.=$x[$i];
		}
		if($x[$i]=="\"" && $x[$i]!="\\") {
			$inquote=!$inquote;
		}
	}
	return($out);
}

$search=array("_ROOTFS_MIME",
						  "_MANIFEST_MIME",
						  "_CONFIG_MIME",
						  "_ROOTFS_TAR_DIGEST", 
						  "_ROOTFS_TARGZ_SIZE",
						  "_ROOTFS_TARGZ_DIGEST");
$replce=array(trim(file_get_contents("config/mime_rootfs")),
				  trim(file_get_contents("config/mime_manifest")),
				  trim(file_get_contents("config/mime_config")),
						  hashF("compress.zlib://rootfs.tar.gz"),
						  filesize("rootfs.tar.gz"),
						  hashF("rootfs.tar.gz"));
file_put_contents("config.json", str_replace($search, $replce, compactJSON(file_get_contents("config.json.in"))));
array_push($search, "_CONFIG_SIZE", 
						  "_CONFIG_DIGEST");
array_push($replce, filesize("config.json"),
						  hashF("config.json"));
file_put_contents("manifest.json", str_replace($search, $replce, compactJSON(file_get_contents("manifest.json.in"))));

