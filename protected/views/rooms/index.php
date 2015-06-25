<div>
    <form class="frm-search row" action="<?php echo Yii::app()->createUrl("rooms/index")  ?>">
        <div class="col-sm-12 col-md-4">
            <div class="form-group">
                <div class="inner-addon left-addon">
                    <i class="fa fa-map-marker"></i>
                    <input type="text" class="form-control input-lg" id="place-desc" name="place" placeholder="<?php echo(Yii::t('app', 'Điểm đến của bạn')) ?>">
                    <input type="hidden" id="place-lat" name="lat" >
                    <input type="hidden" id="place-long" name="long" >
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3 col-md-2">
            <div class="form-group">
                <div class="inner-addon left-addon">
                    <i class="fa fa-calendar"></i>
                    <input type="text" class="form-control input-lg"
                           placeholder="<?= Yii::t('app', 'Nhận phòng') ?>">
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3 col-md-2">
            <div class="form-group">
                <div class="inner-addon left-addon">
                    <i class="fa fa-calendar"></i>
                    <input type="text" class="form-control input-lg" placeholder="<?= Yii::t('app', 'Trả phòng') ?>">
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3 col-md-2">
            <div class="form-group">
                <div class="inner-addon left-addon">
                    <i class="fa fa-users"></i>
                    <input type="text" class="form-control input-lg" placeholder="<?= Yii::t('app', 'Khách') ?>">
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3 col-md-2">
            <button id="search-button" class="btn btn-primary btn-block btn-lg"
                    type="submit"><?= Yii::t('app', 'Tìm kiếm') ?></button>
        </div>

    </form>
</div>
<hr>
<div class="row">
    <div class="col-md-8">
        <div>
            <label style="margin-right: 20px;">Sắp xếp theo:</label>
            <div class="btn-group" role="group" aria-label="...">
                <a href="<?php echo $this->createUrl('', array_merge($_GET, array('sort' => 'review'))) ?>" class="btn btn-default <?php RoomAddress::checkSort('review') ?>">Lượng giới thiệu</a>
                <a href="<?php echo $this->createUrl('', array_merge($_GET, array('sort' => 'price'))) ?>" class="btn btn-default <?php RoomAddress::checkSort('price') ?>">Giá</a>
            </div>
		</div>
        <hr>
        <div class="row">
        <?php $location = array(); ?>
        <?php foreach($model as $room) : ?>
            <?php if($room->distance > 10) break; ?>
            <?php 
                $content = CHtml::link($room->name, array('rooms/view', 'id' => $room->id), array('class' => 'marker-link'));
                $content .= '<div>' . number_format($room->RoomPrice->price) . 'VND</div>';
            ?>
            <?php $location[] = array($content, $room->lat, $room->long, $room->id) ; ?>
            <div class="room-search col-md-6">
                <div class="img-room" >
                    <?php 
                        $images = $room->RoomImages; 
                        if (!empty($images)) {
                            $image = $images[0];
                            echo CHtml::image(Yii::app()->baseUrl . Constant::PATH_UPLOAD_PICTURE . $image->image_name, '', array('class' => 'img-responsive img-show'));
                        }
                    ?>
                    <div class="money-room">
                        <?php echo number_format($room->RoomPrice->price) ?> <sup>VND</sup>
                    </div>
                    <div class="user-room">
                        <?php if(!empty($room->Users->profile_picture)) : ?>
                            <?php echo CHtml::image($room->Users->profile_picture, '', array('class' => 'img-responsive image-user')) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <h4 style="color: #398fd1;"><?php echo CHtml::link($room->name, array('rooms/view', 'id' => $room->id))?></h4>
                <h5>
                <?php 
                    $room_type_title = $room->getRoomType($room->room_type, true);
                    if($room_type_title) echo implode(', ' , $room_type_title) . ' - ';
                    echo $room->district . ' - ' . $room->city;
                ?>
                </h5>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="search-form">
            <h4>Tìm kiếm</h4>
            <div id="map" style="height: 350px;margin-bottom: 10px;"></div>
			<div class="btn-group" data-toggle="buttons">
				<label class="btn btn-info <?php echo RoomAddress::checkRoomtype('entire_home') ?>"><input type="checkbox" autocomplete="off" value="entire_home" name="room_type" <?php echo RoomAddress::checkRoomtype('entire_home', true) ?>> <i class="fa fa-building"></i><br>Cả căn hộ</label> 
				<label class="btn btn-info <?php echo RoomAddress::checkRoomtype('private_room') ?>"> <input type="checkbox" autocomplete="off" value="private_room" name="room_type" <?php echo RoomAddress::checkRoomtype('private_room', true) ?>> <i class="fa fa-user-secret"></i><br>Phòng riêng</label> 
				<label class="btn btn-info <?php echo RoomAddress::checkRoomtype('share_room') ?>"> <input type="checkbox"autocomplete="off" value="share_room" name="room_type" <?php echo RoomAddress::checkRoomtype('share_room', true) ?>> <i class="fa fa-share-alt"></i><br>Phòng chia sẻ</label>
			</div>
        </div>

    </div>
</div>

<?php Yii::app()->clientScript->beginScript('custom-script'); ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
        	jQuery("input[name='room_type']").change(function() {
            	var room_type = [];
        	    jQuery("input[name='room_type']").each(function () {
            	    if(this.checked) {
            	    	room_type.push(jQuery(this).val());
            	    }
        	    });
        	    setGetParameter('room_type', room_type.join());
        	});
        	
        	var autocompleteSearch;
       	    autocompleteSearch = new google.maps.places.Autocomplete((document.getElementById('place-desc')),{ types: ['geocode'] });
       	    google.maps.event.addListener(autocompleteSearch, 'place_changed', searchPlaceChanged);

            function searchPlaceChanged() {
           		var place = autocompleteSearch.getPlace();
           		if (place.geometry) {
           			document.getElementById('place-lat').value =  place.geometry.location.lat();
           		    document.getElementById('place-long').value =  place.geometry.location.lng();
           		}
           	}

            var locations = <?php echo json_encode($location) ?>;

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: new google.maps.LatLng(<?php echo $_GET['lat']?>, <?php echo $_GET['long']?>),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            var infowindow = new google.maps.InfoWindow();

            var marker, i;

            for (i = 0; i < locations.length; i++) {  
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                    map: map
                });

                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                  return function() {
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                  }
                })(marker, i));
            }

            function setGetParameter(paramName, paramValue) {
                var url = window.location.href;
                if (url.indexOf(paramName + "=") >= 0)
                {
                    var prefix = url.substring(0, url.indexOf(paramName));
                    var suffix = url.substring(url.indexOf(paramName));
                    suffix = suffix.substring(suffix.indexOf("=") + 1);
                    suffix = (suffix.indexOf("&") >= 0) ? suffix.substring(suffix.indexOf("&")) : "";
                    url = prefix + paramName + "=" + paramValue + suffix;
                }
                else
                {
                if (url.indexOf("?") < 0)
                    url += "?" + paramName + "=" + paramValue;
                else
                    url += "&" + paramName + "=" + paramValue;
                }
                window.location.href = url;
            }
            
                       	
        });
    </script>
<?php Yii::app()->clientScript->endScript();?>