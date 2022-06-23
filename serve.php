<?php
function mylog($s) {
	file_put_contents("/tmp/oci-dist-log","$s\n",FILE_APPEND);
}

function showfile($f, $mime, $hash) {
	mylog("OK $f");
	header("Content-type: $mime");
	//header("Docker-content-digest: sha256:$hash");
	readfile($f);
	exit;
}

$mimeManifest="application/vnd.oci.image.manifest.v1+json";
$mimeConfig="application/vnd.oci.image.config.v1+json";

/*
$mimeManifest="application/vnd.docker.distribution.manifest.v2+json";
$mimeConfig="application/vnd.docker.container.image.v1+json";
*/

mylog($_SERVER['PATH_INFO']." ".$_SERVER['HTTP_ACCEPT']." ".$_SERVER['HTTP_USER_AGENT']);
if(strpos($_SERVER['PATH_INFO'],"/manifests/")!==false) {
	showfile("manifest.json", $mimeManifest, hash_file("sha256","manifest.json"));
}

$hash_config=hash_file("sha256","config.json");
$hash_rootfs=hash_file("sha256","rootfs.tar.gz");

if(strpos($_SERVER['PATH_INFO'],"/blobs/sha256:")!==false) {
	$clHash=substr($_SERVER['PATH_INFO'], strpos($_SERVER['PATH_INFO'],"/blobs")+strlen("/blobs/sha256:"));
	if($clHash==$hash_config) showfile("config.json", $mimeConfig, $hash_config);
	if($clHash==$hash_rootfs) showfile("rootfs.tar.gz", "application/octet-stream", $hash_rootfs);
	header("HTTP/1.1 404");	print "Blob $clHash not found"; mylog("Blob $clHash not found"); exit;
}

header("HTTP/1.1 404"); print "URL not understood"; mylog("URL not understood"); exit;

