/**
 *  fs.js
 *  Реализация доступа к файловой системе при помощи html5
 *
 **/


function errorHandler(err){
    var msg = 'FileSystem Error: ';

    switch (err.code) {
        case FileError.NOT_FOUND_ERR:
            msg += 'File or directory not found';
            tryCreate();
            break;

        case FileError.NOT_READABLE_ERR:
            msg += 'File or directory not readable';
            break;

        case FileError.PATH_EXISTS_ERR:
            msg += 'File or directory already exists';
            break;

        case FileError.TYPE_MISMATCH_ERR:
            msg += 'Invalid filetype';
            break;

        default:
            msg += 'Unknown Error';
            break;
    };

    console.log(msg);
};

function appendFile(txt){
    window.requestFileSystem  = window.requestFileSystem || window.webkitRequestFileSystem;
    window.requestFileSystem(window.TEMPORARY, 5*1024*1024, function(fs){
        fs.root.getFile('mysterion_log.txt', {create: false}, function(fileEntry) {
            fileEntry.createWriter(function(fileWriter) {
                fileWriter.seek(fileWriter.length);
                //window.BlobBuilder = window.BlobBuilder || window.WebKitBlobBuilder;
                window.BlobBuilder = window.BlobBuilder || window.MozBlobBuilder || window.WebKitBlobBuilder || window.MSBlobBuilder;
                var bb = new window.BlobBuilder();
                bb.append(txt);
                fileWriter.write(bb.getBlob('text/plain'));
            }, errorHandler);
        }, errorHandler);
    }, errorHandler);

    /*function initFS(fs){
        fs.root.getFile('mysterion_log.txt', {create: false}, function(fileEntry) {
            fileEntry.createWriter(function(fileWriter) {
                fileWriter.seek(fileWriter.length);
                window.BlobBuilder = window.BlobBuilder || window.WebKitBlobBuilder;
                var bb = new BlobBuilder();
                bb.append(txt);
                fileWriter.write(bb.getBlob('text/plain'));
            }, errorHandler);
        }, errorHandler);
    }*/
};

function tryCreate(){
    window.requestFileSystem  = window.requestFileSystem || window.webkitRequestFileSystem;
    window.requestFileSystem(window.TEMPORARY, 5*1024*1024, initFS, errorHandler);
    function initFS(fs){
        fs.root.getFile('mysterion_log.txt', {create: true, exclusive: true}, function(fileEntry) {
            console.log('A file ' + fileEntry.name + ' was created successfully.');
        }, errorHandler);
    }
}