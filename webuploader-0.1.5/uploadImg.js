/**
 * Created by 建国 on 2015/2/10.
 */
function bindUploader(containerSelector,valSelector) {
    var $ = jQuery,    // just in case. Make sure it's not an other libaray.

        $wrap = $('#uploader'),

        $queue = $('<ul class="filelist"></ul>')
            .appendTo( $wrap.find('.queueList') ),

        $statusBar = $wrap.find('.statusBar'),

        $info = $statusBar.find('.info'),

        $upload = $wrap.find('.uploadBtn'),

        $placeHolder = $wrap.find('.placeholder'),

        $progress = $statusBar.find('.progress').hide(),

        fileCount = 0,

        fileSize = 0,

        ratio = window.devicePixelRatio || 1,

        thumbnailWidth = 110 * ratio,
        thumbnailHeight = 110 * ratio,

        state = 'pedding',

        percentages = {},

        supportTransition = (function(){
            var s = document.createElement('p').style,
                r = 'transition' in s ||
                    'WebkitTransition' in s ||
                    'MozTransition' in s ||
                    'msTransition' in s ||
                    'OTransition' in s;
            s = null;
            return r;
        })(),

        uploader;

    if ( !WebUploader.Uploader.support() ) {
        alert( 'Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
        throw new Error( 'WebUploader does not support the browser you are using.' );
    }

    uploader = WebUploader.create({
        pick: {
            id: '#filePicker',
            label: '点击选择图片'
        },
        dnd: '#uploader .queueList',
        paste: document.body,

        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        },

        swf: 'Uploader.swf',

        disableGlobalDnd: true,
        chunked: true,
        server: 'index.php?mod=upload&code=image',
        fileNumLimit: 10,
        fileSizeLimit: 5 * 1024 * 1024,    // 200 M
        fileSingleSizeLimit: 1 * 1024 * 1024    // 50 M
    });

    uploader.addButton({
        id: '#filePicker2',
        label: '继续添加'
    });

    function addFile( file ) {
        var $li = $( '<li id="' + file.id + '">' +
            '<p class="title">' + file.name + '</p>' +
            '<p class="imgWrap"></p>'+
            '<p class="progress"><span></span></p>' +
            '</li>' ),

            $btns = $('<div class="file-panel">' +
            '<span class="cancel">删除</span>' +
            '<span class="rotateRight">向右旋转</span>' +
            '<span class="rotateLeft">向左旋转</span></div>').appendTo( $li ),
            $prgress = $li.find('p.progress span'),
            $wrap = $li.find( 'p.imgWrap' ),
            $info = $('<p class="error"></p>'),

            showError = function( code ) {
                switch( code ) {
                    case 'exceed_size':
                        text = '文件大小超出';
                        break;

                    case 'interrupt':
                        text = '上传暂停';
                        break;

                    default:
                        text = '上传失败，请重试';
                        break;
                }

                $info.text( text ).appendTo( $li );
            };

        if ( file.getStatus() === 'invalid' ) {
            showError( file.statusText );
        } else {
            // @todo lazyload
            $wrap.text( '预览中' );
            uploader.makeThumb( file, function( error, src ) {
                if ( error ) {
                    $wrap.text( '不能预览' );
                    return;
                }

                var img = $('<img src="'+src+'">');
                $wrap.empty().append( img );
            }, thumbnailWidth, thumbnailHeight );

            percentages[ file.id ] = [ file.size, 0 ];
            file.rotation = 0;
        }

        file.on('statuschange', function( cur, prev ) {
            if ( prev === 'progress' ) {
                $prgress.hide().width(0);
            } else if ( prev === 'queued' ) {
                $li.off( 'mouseenter mouseleave' );
                $btns.remove();
            }

            // 成功
            if ( cur === 'error' || cur === 'invalid' ) {
                console.log( file.statusText );
                showError( file.statusText );
                percentages[ file.id ][ 1 ] = 1;
            } else if ( cur === 'interrupt' ) {
                showError( 'interrupt' );
            } else if ( cur === 'queued' ) {
                percentages[ file.id ][ 1 ] = 0;
            } else if ( cur === 'progress' ) {
                $info.remove();
                $prgress.css('display', 'block');
            } else if ( cur === 'complete' ) {
                $li.append( '<span class="successFlag"></span>' );
            }

            $li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
        });

        $li.on( 'mouseenter', function() {
            $btns.stop().animate({height: 30});
        });

        $li.on( 'mouseleave', function() {
            $btns.stop().animate({height: 0});
        });

        $btns.on( 'click', 'span', function() {
            var index = $(this).index(),
                deg;

            switch ( index ) {
                case 0:
                    uploader.removeFile( file );
                    return;

                case 1:
                    file.rotation += 90;
                    break;

                case 2:
                    file.rotation -= 90;
                    break;
            }

            if ( supportTransition ) {
                deg = 'rotate(' + file.rotation + 'deg)';
                $wrap.css({
                    '-webkit-transform': deg,
                    '-mos-transform': deg,
                    '-o-transform': deg,
                    'transform': deg
                });
            } else {
                $wrap.css( 'filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((file.rotation/90)%4 + 4)%4) +')');
                // use jquery animate to rotation
                // $({
                //     rotation: rotation
                // }).animate({
                //     rotation: file.rotation
                // }, {
                //     easing: 'linear',
                //     step: function( now ) {
                //         now = now * Math.PI / 180;

                //         var cos = Math.cos( now ),
                //             sin = Math.sin( now );

                //         $wrap.css( 'filter', "progid:DXImageTransform.Microsoft.Matrix(M11=" + cos + ",M12=" + (-sin) + ",M21=" + sin + ",M22=" + cos + ",SizingMethod='auto expand')");
                //     }
                // });
            }


        });

        $li.appendTo( $queue );
    }

    function removeFile( file ) {
        var $li = $('#'+file.id);

        delete percentages[ file.id ];
        updateTotalProgress();
        $li.off().find('.file-panel').off().end().remove();
    }

    function updateTotalProgress() {
        var loaded = 0,
            total = 0,
            spans = $progress.children(),
            percent;

        $.each( percentages, function( k, v ) {
            total += v[ 0 ];
            loaded += v[ 0 ] * v[ 1 ];
        } );

        percent = total ? loaded / total : 0;

        spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
        spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
        updateStatus();
    }

    function updateStatus() {
        var text = '', stats;

        if ( state === 'ready' ) {
            text = '选中' + fileCount + '张图片，共' +
            WebUploader.formatSize( fileSize ) + '。';
        } else if ( state === 'confirm' ) {
            stats = uploader.getStats();
            if ( stats.uploadFailNum ) {
                text = '已成功上传' + stats.successNum+ '张图片，'+
                stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
            }

        } else {
            stats = uploader.getStats();
            text = '共' + fileCount + '张（' +
            WebUploader.formatSize( fileSize )  +
            '），已上传' + stats.successNum + '张';

            if ( stats.uploadFailNum ) {
                text += '，失败' + stats.uploadFailNum + '张';
            }
        }

        $info.html( text );
    }

    function setState( val ) {
        var file, stats;

        if ( val === state ) {
            return;
        }

        $upload.removeClass( 'state-' + state );
        $upload.addClass( 'state-' + val );
        state = val;

        switch ( state ) {
            case 'pedding':
                $placeHolder.removeClass( 'element-invisible' );
                $queue.parent().removeClass('filled');
                $queue.hide();
                $statusBar.addClass( 'element-invisible' );
                uploader.refresh();
                break;

            case 'ready':
                $placeHolder.addClass( 'element-invisible' );
                $( '#filePicker2' ).removeClass( 'element-invisible');
                $queue.parent().addClass('filled');
                $queue.show();
                $statusBar.removeClass('element-invisible');
                uploader.refresh();
                break;

            case 'uploading':
                $( '#filePicker2' ).addClass( 'element-invisible' );
                $progress.show();
                $upload.text( '暂停上传' );
                break;

            case 'paused':
                $progress.show();
                $upload.text( '继续上传' );
                break;

            case 'confirm':
                $progress.hide();
                $upload.text( '开始上传' ).addClass( 'disabled' );

                stats = uploader.getStats();
                if ( stats.successNum && !stats.uploadFailNum ) {
                    setState( 'finish' );
                    return;
                }
                break;
            case 'finish':
                stats = uploader.getStats();

                if ( stats.successNum ) {
                    var list = $.dialog.list;
                    for( var i in list ){
                        list[i].close();
                    }
                } else {
                    state = 'done';
                    location.reload();
                }
                break;
        }

        updateStatus();
    }

    uploader.onUploadProgress = function( file, percentage ) {
        var $li = $('#'+file.id),
            $percent = $li.find('.progress span');

        $percent.css( 'width', percentage * 100 + '%' );
        percentages[ file.id ][ 1 ] = percentage;
        updateTotalProgress();
    };

    uploader.onFileQueued = function( file ) {
        fileCount++;
        fileSize += file.size;

        if ( fileCount === 1 ) {
            $placeHolder.addClass( 'element-invisible' );
            $statusBar.show();
        }

        addFile( file );
        setState( 'ready' );
        updateTotalProgress();
    };

    uploader.onFileDequeued = function( file ) {
        fileCount--;
        fileSize -= file.size;

        if ( !fileCount ) {
            setState( 'pedding' );
        }

        removeFile( file );
        updateTotalProgress();

    };

    uploader.on( 'all', function( type ) {
        var stats;
        switch( type ) {
            case 'uploadFinished':
                setState( 'confirm' );
                break;

            case 'startUpload':
                setState( 'uploading' );
                break;

            case 'stopUpload':
                setState( 'paused' );
                break;
        }
    });
    uploader.onError = function( code ) {
        if(code=='F_DUPLICATE')
            return;
        else
            alert( 'Eroor: ' + code );
    };
    uploader.onUploadSuccess = function( file,response  ) {
        var json = eval('(' + response._raw + ')');
        var view = $(containerSelector);
        var imgspath = $(valSelector);
        if(view.size()>0) {
            var html = [];
            html.push('<li>');
            html.push('	<p class="imgWrap"><img src="' + json.file.path + '"></p>');
            html.push('	<div class="file-panel" style="height: 0px; overflow: hidden;">');
            html.push('		<span class="cancel" data-id="'+json.file.id+'">删除</span>');
            html.push('	</div>');
            html.push('</li>');
            html = html.join('');
            view.append(html);
            console.log(view.find('li:eq('+(view.find('li').size()-1)+')'));
            view.find('li:eq('+(view.find('li').size()-1)+')').hover(function(){
                $(this).find('.file-panel').animate({height:'30px'},500);
            },function(){
                $(this).find('.file-panel').animate({height:'0'},500);
            }).find('.file-panel .cancel')
                .click(function(){
                    var rid = $(this).data('id');
                    $.post('index.php?mod=upload&code=deleteImg',{ id: rid });
                    if(imgspath.size()>0) {
                        var ids = imgspath.val().split(',');
                        var i = -1;
                        for (var index in ids) {
                            if (ids[index] == rid) {
                                i = index;
                                break;
                            }
                        }
                        if (i < 0)return;
                        ids.splice(i, 1);
                        imgspath.val(ids.join(','));
                        $(this).parents('li').remove();
                    }
                });
        }
        if(imgspath.size()>0) {
            var val = imgspath.val();
            if ($.trim(val) == "")imgspath.val(json.file.id);
            else {
                imgspath.val(val + ',' + json.file.id);
            }
        }
    };

    $upload.on('click', function() {
        if ( $(this).hasClass( 'disabled' ) ) {
            return false;
        }

        if ( state === 'ready' ) {
            uploader.upload();
        } else if ( state === 'paused' ) {
            uploader.upload();
        } else if ( state === 'uploading' ) {
            uploader.stop();
        }
    });

    $info.on( 'click', '.retry', function() {
        uploader.retry();
    } );

    $info.on( 'click', '.ignore', function() {
        alert( 'todo' );
    } );

    $upload.addClass( 'state-' + state );
    updateTotalProgress();
}

jQuery.fn.uploadImgs = function(containerSelector,valSelector){
    var htmlUplad = [];
    htmlUplad.push('<div id="uploader" class="wu-example">');
    htmlUplad.push('        <div class="queueList">');
    htmlUplad.push('            <div id="dndArea" class="placeholder">');
    htmlUplad.push('                <div id="filePicker" class="webuploader-container"><div class="webuploader-pick">');
    htmlUplad.push('                    点击选择图片');
    htmlUplad.push('                </div>');
    htmlUplad.push('            </div>');
    htmlUplad.push('            <p>或将照片拖到这里，单次最多可选300张</p>');
    htmlUplad.push('        </div>');
    htmlUplad.push('        <ul class="filelist"></ul>');
    htmlUplad.push('        </div>');
    htmlUplad.push('        <div class="statusBar" style="display:none;">');
    htmlUplad.push('            <div class="progress" style="display: none;">');
    htmlUplad.push('                <span class="text">0%</span>');
    htmlUplad.push('                <span class="percentage" style="width: 0%;"></span>');
    htmlUplad.push('            </div><div class="info">共0张（0B），已上传0张</div>');
    htmlUplad.push('            <div class="btns">');
    htmlUplad.push('                <div id="filePicker2" class="webuploader-container">');
    htmlUplad.push('                </div>');
    htmlUplad.push('                <div class="uploadBtn state-pedding">开始上传</div>');
    htmlUplad.push('            </div>');
    htmlUplad.push('        </div>');
    htmlUplad.push('    </div>');
    htmlUplad = htmlUplad.join('');
    this.openLoadWin =  function(){
        $.dialog({content: htmlUplad,lock:true,fixed:true,width:600,title:'图片上传',max:false,min:false});
        bindUploader(containerSelector,valSelector);
    }
    $(this).click(this.openLoadWin);
};