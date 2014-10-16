/**
  * The real search.
  * @author Marius Žilėnas
  * @version 0.0.1
  */

function gbid(name) {
	return document.getElementById(name);
}

function hide(id) {
	f = gbid(id);
	if(f !== null) f.style.display = "none";
}

function show(id) {
	f = gbid(id);
	if(f !== null) f.style.display = "block";
}
function isHidden(id) {
	f = gbid(id);
	return f !== null && 'none' === f.style.display;
}

/**
 * Library for different simple functions.
 */
function Lib() {}
/**
 * Checks whether variable exists in array. Checks with === .
 *
 * @param {Object} v
 *
 * @param {Array} arr
 *
 * @returns {Boolean}
 */
Lib.contains = function(v, arr) {
	var i   = 0;
	var has = false; 
	for(i = 0; !has || i < arr.length; i++) 
		if(arr[i] === v) has = true;
	return has;
}
/**
 * Uppercases first character of the string.
 *
 * @param {String} $str String to uppercase.
 *
 * @returns {String}
 */
Lib.ucFirst = function(str) {
	return str.charAt(0).toUpperCase() + str.substr(1);
}

/**
 * Returns object class name.
 *
 * @param {Object} o
 *
 * @returns {String}
 */
Lib.uok = function(o) {
	return Lib.ucFirst(Object.keys(o)[0]);
}

/**
 * Generates random integer between 0 and 2^(32-1).
 *
 * @returns {Integer}
 */
Lib.rint = function() {
	return Math.floor(Math.random()*Math.pow(2,32-1));
}

/**
 * Adds timeout to remove element with id after ms seconds passed.
 * 
 * @param {String} id
 *
 * @param {Integer} ms
 */
Lib.tRemove = function(id, ms) {
	window.setTimeout(function() {
		var e = gbid(id);
		if('undefined' !== typeof e)
			e.parentNode.removeChild(e);
		},
		ms);
}

/**
 * Adds message to 'msgs' div.
 */
Lib.m = function(s) {
	msgs = gbid('msgs');
	msgs.innerHTML = msgs.innerHTML + s;
}

/**
 * Adds info message to msgs box.
 * 
 * @param {String} msg
 */
Lib.info = function(msg) {
	var r  = Lib.rint();
	var id = 'info_'+r; 
	Lib.m('<span class="info" id="'+id+'">'+msg+'</span>');
	Lib.tRemove(id,WindowManager.max_msg_duration());
}

/**
 * Adds error message to msgs box.
 * 
 * @param {String} msg
 */
Lib.error = function(msg) {
	var r  = Lib.rint();
	var id = 'error_'+r; 
	Lib.m('<span class="error" id="'+id+'">'+msg+'</span>');
	Lib.tRemove(id,WindowManager.max_msg_duration());
}

/**
  * Returns position x,y of the element.
  * 
  * @param {Object} element
  * 
  * @returns {Array}
  */
Lib.xy = function(element) {
	var x = 0;
	var y = 0;
	while(element) { 
		x += (element.offsetLeft - element.scrollLeft + element.clientLeft);
		y += (element.offsetTop - element.scrollTop + element.clientTop);
		element = element.offsetParent;
	}
	
	return {"x" : x, "y" : y};
}

/**
 * Convert string form json object to object of Classname.
 * 
 * @param {Object} $tjson Json object.
 *
 * @param {String} $classname Class name of the object to create.
 *
 * @returns {Object}
 */
Lib.funcFromJsonO = function(tjson, classname) { 
	var o   = new window[classname](); 
	var ks  = Object.keys(tjson);
	var i   = 0;
	var key = ''; 
	for(i = 0; i<ks.length; i++) {
		key = ks[i];
		if('undefined' === typeof o["set_"+key]) {//if doesn't have setter
			o["m_"+key] = tjson[key];
		} else {
			o["set_"+key](tjson[key]);
		}
	}
	return o;
}

/**
 * Creates HttpObject.
 *
 * @returns {Object}
 */
Lib.makeHttpObject = function() {
	try { 
		return new XMLHttpRequest();
	}
	catch (error) {}

	try {
		return new ActiveXObject("Msxml2.XMLHTTP");
	}
	catch(error) {}

	try {
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
	catch (error) {}

	throw new Error("Could not create HTTP request object.");
}

/**
 * Makes request.
 */
Lib.request = function(ho, url, postdata, success, failure) {
	var async = true;
	if(null !== postdata) {
		ho.open("POST", url, async);
		ho.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		ho.send(postdata);
	} else {
		ho.open("GET", url, async);
		ho.send(null);
	}
	ho.onreadystatechange = function() {
		if(ho.readyState == 4) {
			if(success && ho.status == 200) {
				success(ho.responseText);
			} else if(failure) {
				failure(ho.status, ho.statusText);
			}
		}
	}
}

function Jsonp() { } 
/**
 * This validation regexp is from RFC 4627 Chapter 6
 */
Jsonp.secureObjConvert = function(str) {
	return !(/[^,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/.test(str.replace(/"(\\.|[^"\\])*"/g, ''))) && eval('(' + str + ')'); 
}

/**
 * Makes object from json string.
 *
 * @param {String} str Json string response (usualy it is from server).
 *
 * @returns {Array}
 */
Jsonp.asjobjs = function(str) {

	var jobjs   = this.secureObjConvert(str);
	var cl      = Lib.uok(jobjs);
	var tjsonos = jobjs[cl]; 
	var objs    = new Array();
	var tjsono; //temporary
	var i = 0;

	for(i = 0; i<tjsonos.length; i++) {
		tjsono = tjsonos[i];
		objs.push(window[cl]['fromJsonO'](tjsono, cl));
	}
	return objs; 
}

/**
 * Request.
 */
function Req() { }

/**
 * Setter for url.
 *
 * @param {String} $url
 */
Req.prototype.setUrl = function(url) {
	this.m_url = url;
}
Req.prototype.url = function() {
	return this.m_url;
}
Req.prototype.setSuccess = function(success) {
	this.m_success = success;
}
Req.prototype.success = function() {
	return this.m_success;
}
Req.prototype.setFailure = function(failure) {
	this.m_failure = failure;
}
Req.prototype.failure = function() {
	return this.m_failure;
}
Req.prototype.post_data = function() {
	return this.m_post_data;
}
Req.prototype.setPostData = function(pd) {
	this.m_post_data = pd;
}
Req.prototype.isPost = function() {
	return typeof this.m_post_data !== 'undefined';
}

/**
 * Execute request.
 *
 * @private
 *
 * @param {Object} $ho HttpObject to execute request through.
 */
Req.prototype.execute = function(ho) {
	if(this.isPost()) {
		Lib.request(ho, this.url(), this.post_data(), this.success(), this.failure());
	} else {
		Lib.request(ho, this.url(), null, this.success(), this.failure());
	}
}

/**
 * Requests manager. 
 *
 * Puts all requests to the queue. Executes requests one after another.
 */
function XR() {
	this.m_requests = new Array();
	this.m_current  = null; // current request
	this.m_ho       = Lib.makeHttpObject();
}

XR.prototype.eventGotResponse = function(response) {
	if(response.success()) {
		xr.executedSuccessfully();
	} else {
		xr.executedUnsuccessfully();
	}
}
/**
 * Method to request executed successfully.
 */
XR.prototype.executedSuccessfully = function() {
	this.setCurrent(null);
}

/**
 * Method to tell that request executed unsuccessfully.
 */
XR.prototype.executedUnsuccessfully = function() {
	this.setCurrent(null);
}

/**
 * Returns requests.
 *
 * @returns {Array}
 */
XR.prototype.requests = function() {
	return this.m_requests;
}

/**
 * Gets next request from queue.
 *
 * @private
 * 
 * @returns {Object}
 */
XR.prototype.setCurrentNext = function() {
	if(this.requests().length > 0) {
		//Take one request from queue.
		r = this.requests().shift();
		//make it current request.
		this.setCurrent(r);
		return this.current();
	}
}

/**
 * Tells whether is executing current request.
 */
XR.prototype.isExecuting = function() {
	return this.current() !== null;
}

/**
 * Executes next request from queue.
 *
 * @private
 */
XR.prototype.executeNext = function() {
	if(!this.isExecuting() && this.requests().length > 0) {
		this.setCurrentNext();
		this.current().execute(this.ho());
	}
} 
XR.prototype.current = function() {
	return this.m_current;
} 
XR.prototype.setCurrent = function(r) {
	this.m_current = r;
} 
XR.prototype.ho = function() {
	return this.m_ho;
}

/**
 * Adds POST request to the end of queue.
 */
XR.prototype.addPost = function(url, postdata, success, failure) {
	r = new Req();
	r.setUrl(url);
	r.setSuccess(success);
	r.setFailure(failure);
	r.setPostData(postdata);
	this.addIfNotExists(r);
}

/**
 * Adds GET request to the end of queue.
 */
XR.prototype.addRequest = function(url, success, failure) {
	r = new Req();
	r.setUrl(url);
	r.setSuccess(success);
	r.setFailure(failure);
	this.addIfNotExists(r);
}
/**
 * Checks if has such request.
 *
 * @param {Request} $rtc Request to check for.
 *
 * @returns {Boolean}
 */
XR.prototype.hasRequest = function(rtc) {
	var i   = 0;
	var r   = null;
	var has = false;
	for(i = 0; i<xr.requests().length; i++) {
		r = xr.requests()[i];
		if(rtc.url() == r.url()) has = true;
		if(has) break;
	}
	return has;
}
/**
 * Add request if not exists in queue.
 *
 * @param {Req} r
 */
XR.prototype.addIfNotExists = function(r) {
	if(!xr.hasRequest(r))
		xr.requests().push(r);
}

/**
 * What to do on each iteration.
 */
XR.prototype.pulse = function() {
	xr.executeNext();
}

xr = new XR();
window.setInterval(xr.pulse, 1000);

/**
 * Data returned from api.
 */
function ApiData() {
	this.m_dbobjs   = new Object(); //use Object for Associative arrays.
	this.m_response = null; 
}

/**
 * Setter for m_response.
 *
 * @param {Object} $response
 */
ApiData.prototype.set_response = function(response) {
	this.m_response = response;
}

/**
 * Getter for m_response.
 *
 * @returns {Object}
 */
ApiData.prototype.response = function() {
	return this.m_response;
}

/**
 * Gives dbobjs by class name.
 *
 * @param {String} cn Class name of objects.
 *
 * @returns {Array}
 */
ApiData.prototype.dbobjsbycn = function(cn) {
	var o   = this.dbobjs();
	return ('undefined' === typeof o[cn]) ? new Array() : o[cn];
}

/**
 * Getter for m_dbobjs.
 */
ApiData.prototype.dbobjs = function() {
	return this.m_dbobjs;
} 

/**
 * Adds dbobjs.
 *
 * @param {Array} $dbobjs
 *
 * @param {String} $cn Class name of objects.
 */
ApiData.prototype.set_dbobjs = function(dbobjs, cn) {
	this.m_dbobjs[cn] = dbobjs;
}

/**
 * Returns object of class ApiData.
 *
 * @param {String} str Data string.
 *
 * @returns {Object}
 */
ApiData.toObj = function(str) {
	// Parse string.
	var apijobj = Jsonp.secureObjConvert(str); //object from server data
	var ad = new ApiData();

	//Database objects
	if('undefined' !== typeof apijobj.DbObjs) { 
		var dbobjs = apijobj.DbObjs;
		var classnames = Object.keys(dbobjs);
		var i = 0;
		var j = 0;
		// With each class extract it's objects
		for(i = 0; i < classnames.length; i++) {
			var cn   = classnames[i];
			var objs = new Array();
			for(j = 0; j < dbobjs[cn].length; j++ )
				objs.push(window[cn]['fromJsonO'](dbobjs[cn][j], cn)); 
			ad.set_dbobjs(objs, cn);
		}
	}
	
	//Response
	ad.set_response(Response.fromJsonO(apijobj.Response, "Response")); 
	return ad;
}

/**
 * Response.
 */
function Response() {}
Response.fromJsonO = Lib.funcFromJsonO;

/**
 * Getter for m_success.
 * 
 * @returns {Boolean}
 */
Response.prototype.success = function() {
	return this.m_success;
}

/**
 * Getter for m_message.
 *
 * @returns {String}
 */
Response.prototype.message = function() {
	return this.m_message;
}

/**
 * TALK
 */
function Talk() {}
Talk.fromJsonO = Lib.funcFromJsonO;
/**
 * Getters for Talk.
 */
Talk.prototype.id = function() {
	return this.m_id;
}
Talk.prototype.created_on = function() {
	return this.m_created_on;
}
Talk.prototype.created_on_d = function() {
	return new Date(Date.parse(this.m_created_on));
}
Talk.prototype.what = function() {
	return this.m_what;
}
Talk.prototype.talker_nickname = function() {
	return this.m_talker_nickname;
}

/**
  * Manages search.
  */
function Manager() {}

/**
  * Returns property name input field.
  *
  * @returns Object
  */
Manager.input_property = function() {
	return gbid('property_name');
}

/**
  * Returns span for fields.
  */
Manager.span_fields = function() {
	return gbid('fields');
}

/**
  * Returns div for properties dropdown.
  */
Manager.div_properties = function() {
	return gbid('properties');
}

/**
  * Field name input key pressed.
  * @event
  */
function event_property_name_key_pressed() {
	var pin = Manager.input_property();
	var val = pin.value;
	gw.field_name(val);
}

function Field() {}
Field.fromJsonO = Lib.funcFromJsonO; 
Field.T_TEXT    = 1;
Field.T_NUMERIC = 2;
Field.T_BOOLEAN = 3;

/**
  * Getter for m_type.
  * 
  * @returns Integer
  */
Field.prototype.type = function() {
	return this.m_type;
}

/**
  * Setter for m_type.
  *
  * @param Integer value
  */
Field.prototype.set_type = function(value) { 
	this.m_type = value;
}

/**
  * Is field numeric?
  * 
  * @return boolean
  */
Field.prototype.is_numeric = function() {
	return this.T_NUMERIC == this.type();
}

/**
  * Is field text?
  * 
  * @return boolean
  */
Field.prototype.is_text = function() {
	return this.T_TEXT == this.type();
}

/**
  * Is field boolean?
  * 
  * @return boolean
  */
Field.prototype.is_boolean = function() {
	return this.T_BOOLEAN == this.type();
}

/**
 * Exchanges data with server.
 */
function Gateway() {} 

/**
  * Register request to get field names.
  *
  * @param String search_str String to use for field name matching.
  *
  */
Gateway.prototype.field_name = function(search_str) {
	xr.addRequest("api.php?field_names&search_str="+encodeURIComponent(search_str), gw.event_got_field_names);
}

Gateway.prototype.event_got_field_names = function(str) {
	var ad = ApiData.toObj(str);
	xr.eventGotResponse(ad.response());
	var fields = ad.dbobjsbycn("Field");
	Builder.dropdown(fields);
}

/**
  * Builds drop down HTML.
  */
function Builder() {}

/**
  * Builds dropdown.
  *
  * @param {Array} fields
  */
Builder.dropdown = function(fields) {
	var i    = 0;
	var pdiv = Manager.div_properties(); 
	var html = '';
	var field;
	var tbox = Manager.input_property();
	var pos;
	
	/** Position div under text box. */ 

	for(i = 0 ; i < fields.length; i++) { 
		field = fields[i];
		html += '<span class="el bl" onmouseover="event_properties_drop_down_el_mouseover(this)" onmouseout="event_properties_drop_down_el_mouseout(this)" onclick="event_properties_drop_down_clicked(\''+field.m_name+'\', '+field.type()+')">'+field.m_name+'</span>';  
	}
	pdiv.innerHTML      = html;

	pos = Lib.xy(tbox);
	pdiv.style.position = "absolute";
	pdiv.style.left     = pos.x+"px";
	pdiv.style.top      = 21+pos.y+"px";
	pdiv.style.zIndex   = 999;
}

/**
 * Drop down.
 */
function DropDown() {}

/**
 * Setter for data source.
 *
 * @param {Function} func Function to get data.
 */
DropDown.prototype.set_source = function(gw_method) {
}

/**
 * Setter for input field to attach to. 
 *
 * @param {Object} inp Input field.
 */
DropDown.prototype.attach = function(inp) {
}

/**
 * Get data from data source.
 */
DropDown.prototype.get_data = function() {
} 

/**
 * Show drop down.
 */
DropDown.prototype.appear = function() {
}

/**
 * Hide drop down.
 */
DropDown.prototype.disappear = function() {
}

Builder.drop_down_empty = function() {
	var dd = Manager.div_properties();
	dd.innerHTML = '';
}

function event_properties_drop_down_el_mouseover(el) {
	el.className = 'ddownsel';
}

function event_properties_drop_down_el_mouseout(el) {
	el.className = 'ddown';
}

function remove_field(id) {
	gbid(id).innerHTML = '';
}

/**
  * Drop down element clicked.
  * 
  * @param {String} name Field name.
  *
  * @param {Integer} type Field type.
  */
function event_properties_drop_down_clicked(name, type) { 
	/** Hide dropdown */
	/** Create form fields */
	/** Create hidden form field. */
	var fid = "field_"+Lib.rint();
	var inp = '';
	inp = '<input type="hidden" name="add_field" value="'+name+'" />';
	var span_fields = Manager.span_fields();
	span_fields.innerHTML += inp;
	gbid('form_real').submit();
}

var gw = new Gateway();

