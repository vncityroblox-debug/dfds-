<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
$title = 'Mã Hóa Code ionCube';
if (!@$user) {
    new Redirect('/login');
    exit;
}
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');
?>
<section class="py-110 bg-offWhite">
        <div class="container">
            <div class="rounded-3">

                <section class="space-y-6">
                    <div class="row">
                        <div class="col-md-6 mb-5">
                            <div class="profile-info-card">
                                <!-- Header -->
                                <div class="profile-info-header">
                                    <h4 class="text-18 fw-semibold text-dark-300">
                                        MÃ HOÁ IONCUBE
                                    </h4>
                                </div>
                                <div class="profile-info-body bg-white">
                                    <div class="mb-3">
                                        <label for="code_default" class="form-label">Đoạn Code Cần Mã Hoá</label>
                                    <textarea class="form-control" id="code_default" rows="10"></textarea>
                                </div>
                                <div class="mb-3">
                                        <label for="ioncube" class="form-label">Phiên bản ioncube</label>
                                        <select class="form-select shadow-none" id="ioncube" onchange="AiMonney()" required>
                                       <option value="">---- vui lòng chọn phiên bản ioncube ----</option>
                                            <option value="10.3">ionCube 10.3 </option>
                                        </select>
                                    </div>
                                <div class="mb-3">
                                        <label for="php" class="form-label">Phiên bản PHP</label>
                                        <select class="form-select shadow-none" id="php" required>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="btn_submit">Mã Hoá Ngay</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-5">
                            <div class="profile-info-card">
                                <div class="profile-info-header">
                                    <h4 class="text-18 fw-semibold text-dark-300">
                                        KẾT QUẢ
                                    </h4>
                                </div>
                                <div class="profile-info-body bg-white">
                                    <textarea class="form-control" id="code_encode" rows="10" readonly></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</main>

<script>
var _0x47b8aa=_0x1472;function _0x1472(_0x3d718c,_0x22b1b4){var _0x58f8e6=_0x58f8();return _0x1472=function(_0x147214,_0x39c504){_0x147214=_0x147214-0xa8;var _0x5a481d=_0x58f8e6[_0x147214];return _0x5a481d;},_0x1472(_0x3d718c,_0x22b1b4);}(function(_0x172ce5,_0x165810){var _0x9ab3e1=_0x1472,_0x280be8=_0x172ce5();while(!![]){try{var _0x4f6360=-parseInt(_0x9ab3e1(0xbf))/0x1*(-parseInt(_0x9ab3e1(0xba))/0x2)+parseInt(_0x9ab3e1(0xac))/0x3+parseInt(_0x9ab3e1(0xb2))/0x4+parseInt(_0x9ab3e1(0xc1))/0x5*(parseInt(_0x9ab3e1(0xd2))/0x6)+parseInt(_0x9ab3e1(0xbd))/0x7+-parseInt(_0x9ab3e1(0xd7))/0x8+-parseInt(_0x9ab3e1(0xbc))/0x9;if(_0x4f6360===_0x165810)break;else _0x280be8['push'](_0x280be8['shift']());}catch(_0x25b70c){_0x280be8['push'](_0x280be8['shift']());}}}(_0x58f8,0x42f24));function copyToClipboard(_0x4a2ecd){var _0x5e3880=_0x1472;const _0x35d5c4=document[_0x5e3880(0xc6)](_0x5e3880(0xca));_0x35d5c4['value']=_0x4a2ecd,document[_0x5e3880(0xd9)][_0x5e3880(0xc9)](_0x35d5c4),_0x35d5c4[_0x5e3880(0xc7)](),document[_0x5e3880(0xc5)](_0x5e3880(0xb7)),document[_0x5e3880(0xd9)]['removeChild'](_0x35d5c4),showMessage('Đã\x20sao\x20chép\x20vào\x20bộ\x20nhớ\x20tạm.',_0x5e3880(0xcc));}function _0x58f8(){var _0x536883=['<i\x20class=\x22fa\x20fa-spinner\x20fa-spin\x22></i>\x20Đang\x20xử\x20lý...','Không\x20thể\x20xử\x20lý!','php','140900CNlzVs','status','\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<option\x20value=\x2272\x22>PHP\x207.2\x20</option>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<option\x20value=\x2271\x22>PHP\x207.1\x20</option>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<option\x20value=\x2256\x22>PHP\x205.6\x20</option>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<option\x20value=\x2255\x22>PHP\x205.5\x20</option>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<option\x20value=\x2254\x22>PHP\x205.4\x20</option>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<option\x20value=\x2253\x22>PHP\x205.3\x20</option>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<option\x20value=\x225\x22>PHP\x205\x20</option>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<option\x20value=\x224\x22>PHP\x204\x20</option>\x0a\x20\x20\x20\x20\x20\x20\x20\x20','/model/ioncube','#code_encode','copy','value','msg','44wnKyXN','Thông\x20báo!','3586347WYlTJJ','1502508bWARVQ','disabled','21523gIeOzF','Ok,\x20hiểu\x20rồi!','5REkqIP','fire','POST','ajax','execCommand','createElement','select','preventDefault','appendChild','textarea','click','success','trim','location','#php','Mã\x20hóa\x20Ngay','innerHTML','1533498BtenKi','Bạn\x20chưa\x20nhập\x20bất\x20cứ\x20mã\x20code\x20nào!','error','html','#btn_submit','2623424ddyrdQ','warning','body','prop','#code_default','10.3','val','64995jmDBGF','link_download','getElementById'];_0x58f8=function(){return _0x536883;};return _0x58f8();}$('#code_encode')['on'](_0x47b8aa(0xcb),function(){var _0x2a40aa=_0x47b8aa;const _0x33be17=$(this)[_0x2a40aa(0xab)]();if(_0x33be17[_0x2a40aa(0xcd)]()===''){showMessage('Chưa\x20có\x20đoạn\x20code\x20mã\x20hóa\x20nào\x20để\x20sao\x20chép!',_0x2a40aa(0xd4));return;}copyToClipboard(_0x33be17);}),$(_0x47b8aa(0xd6))['on'](_0x47b8aa(0xcb),function(_0x432547){var _0x268a83=_0x47b8aa;_0x432547[_0x268a83(0xc8)](),$('#btn_submit')[_0x268a83(0xd5)](_0x268a83(0xaf))[_0x268a83(0xa8)]('disabled',!![]);var _0x810877=$('#ioncube')[_0x268a83(0xab)]();if(!_0x810877){swal[_0x268a83(0xc2)]({'title':_0x268a83(0xbb),'text':'Bạn\x20chưa\x20chọn\x20bất\x20cứ\x20phiên\x20bản\x20ionCube\x20nào!','icon':_0x268a83(0xd8),'confirmButtonText':_0x268a83(0xc0)}),$(_0x268a83(0xd6))[_0x268a83(0xd5)](_0x268a83(0xd0))['prop'](_0x268a83(0xbe),![]);return;}var _0x1487d0=$(_0x268a83(0xa9))[_0x268a83(0xab)]();if(!_0x1487d0){swal[_0x268a83(0xc2)]({'title':_0x268a83(0xbb),'text':_0x268a83(0xd3),'icon':_0x268a83(0xd8),'confirmButtonText':_0x268a83(0xc0)}),$(_0x268a83(0xd6))['html'](_0x268a83(0xd0))[_0x268a83(0xa8)](_0x268a83(0xbe),![]);return;}$[_0x268a83(0xc4)]({'url':_0x268a83(0xb5),'method':_0x268a83(0xc3),'dataType':'JSON','data':{'code_default':_0x1487d0,'php':$(_0x268a83(0xcf))[_0x268a83(0xab)](),'ioncube':_0x810877},'success':function(_0x3875ce){var _0x41db43=_0x268a83;_0x3875ce[_0x41db43(0xb3)]=='success'?($(_0x41db43(0xb6))[_0x41db43(0xd5)](_0x3875ce[_0x41db43(0xb9)]),_0x3875ce[_0x41db43(0xad)]&&(window[_0x41db43(0xce)]['href']=_0x3875ce['link_download'])):swal[_0x41db43(0xc2)]({'title':_0x41db43(0xbb),'text':_0x3875ce[_0x41db43(0xb9)],'icon':_0x41db43(0xd4),'confirmButtonText':_0x41db43(0xc0)}),$('#btn_submit')[_0x41db43(0xd5)](_0x41db43(0xd0))[_0x41db43(0xa8)](_0x41db43(0xbe),![]);},'error':function(){var _0x31bfcb=_0x268a83;swal[_0x31bfcb(0xc2)]({'title':_0x31bfcb(0xbb),'text':_0x31bfcb(0xb0),'icon':'error','confirmButtonText':_0x31bfcb(0xc0)}),$(_0x31bfcb(0xd6))[_0x31bfcb(0xd5)]('Mã\x20hóa\x20Ngay')[_0x31bfcb(0xa8)](_0x31bfcb(0xbe),![]);}});});function AiMonney(){var _0x4493f6=_0x47b8aa;const _0x519a79=document['getElementById']('ioncube')[_0x4493f6(0xb8)];var _0x527280=document[_0x4493f6(0xae)](_0x4493f6(0xb1)),_0x584837='';_0x519a79==_0x4493f6(0xaa)&&(_0x584837=_0x4493f6(0xb4)),_0x527280[_0x4493f6(0xd1)]=_0x584837;}
</script>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>