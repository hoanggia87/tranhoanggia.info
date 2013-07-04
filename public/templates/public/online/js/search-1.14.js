var lbSearch = new function() {
    var TAB = {
        GoogleSearch: 1,
        Music: 2,
        Image: 3,
        News: 4,
        Maps: 5,
        Video: 6,
        Translate: 7,
        Question: 8,
        ZingSearch: 9
    }
    
    var DEFAULT_TEXT = {};
    DEFAULT_TEXT[TAB.GoogleSearch] = "Tìm kiếm với Google...";
    DEFAULT_TEXT[TAB.Music] = "Tìm kiếm với Zing Mp3...";
    DEFAULT_TEXT[TAB.Image] = "Tìm kiếm với Goolge Hình ảnh...";
    DEFAULT_TEXT[TAB.News] = "Tìm kiếm với Google Tin tức...";
    DEFAULT_TEXT[TAB.Maps] = "Tìm kiếm với Goolge Maps...";
    DEFAULT_TEXT[TAB.Video] = "Tìm kiếm với Goolge Video...";
    DEFAULT_TEXT[TAB.Translate] = "Tìm kiếm với Google Dịch...";
    //DEFAULT_TEXT[TAB.Question] = "Tìm kiếm với Google Hỏi đáp...";
    
    var CSS_CLASS = {};
    CSS_CLASS[TAB.GoogleSearch] = "google";
    CSS_CLASS[TAB.Music] = "zingmp3";
    CSS_CLASS[TAB.Image] = "googleimg";
    CSS_CLASS[TAB.News] = "googlenews";
    CSS_CLASS[TAB.Maps] = "googlemap";
    CSS_CLASS[TAB.Video] = "googlevideo";
    CSS_CLASS[TAB.Translate] = "googletranslate";
    //CSS_CLASS[TAB.Question] = "yahooanswer";
    
    var URL = {};
    URL[TAB.GoogleSearch] = "http://www.google.com.vn/search?client=aff-zing";
    URL[TAB.Music] = "http://mp3.zing.vn/tim-kiem/bai-hat.html?";
    URL[TAB.Image] = "http://www.google.com.vn/images?client=aff-zing";
    URL[TAB.News] = "http://www.google.com.vn/news?client=aff-zing";
    URL[TAB.Maps] = "http://www.google.com.vn/maps?client=aff-zing";
    URL[TAB.Video] = "https://www.google.com.vn/search?tbm=vid&hl=vi&client=aff-zing";
    URL[TAB.Translate] = "http://translate.google.com.vn/?tl=vi&client=aff-zing";
    //URL[TAB.Question] = "http://www.google.com.vn/giaidap/search?client=aff-zing";
    
    var searchType;
    var searchBox = $("#header .search_box");
    if (searchBox.length == 0) {
        searchBox = $("#content .search_category");
    }
    
    var inputBox = $("#txtSearchBox");
    var currentKeyword = "";
    
    var autoCompleteOptions = {
        minChars: 1,
        delay:50,
        scrollHeight: 350,
        searchResponse: false,
        matchCase: false,
        method: "getjson",
        query: "q",
        autoFill: true
    };
    
    var onFocus = function(e) {
        var val = inputBox.val();
        if (!searchType || !DEFAULT_TEXT[searchType]) {
            return;
        }
        if (val == DEFAULT_TEXT[searchType]) {
            inputBox.val("");
        }
        
    };
    
    var onBlur = function(e) {
        if (!searchType || !DEFAULT_TEXT[searchType]) {
            return;
        }
        
        var val = inputBox.val();
        
        if (val == DEFAULT_TEXT[searchType]) {
            currentKeyword = "";
        } else if (val.length > 0) {
            currentKeyword = val;
            return;
        }
        
        //inputBox.val(DEFAULT_TEXT[searchType]);
    };
    
    var onKeyPress = function(e) {
        if (e.keyCode == 13) {
            doSearch(e);
        }
    };
    
    var doSearch = function(e) {
        var val = inputBox.val();
                
        if (val == "" || val == DEFAULT_TEXT[searchType]) {
            //inputBox.val("").focus();
            return;
        }
        
        currentKeyword = val;
        
        log('', 5, 0, searchType);
        
        var searchLink;
        var newTab = true;
        
        if (location.pathname.indexOf('/search') == 0) {
            newTab = false;
        }
        
        if (searchType == TAB.GoogleSearch) {
            searchLink = "/search.html?q=" + encodeURIComponent(val);
        } else if (searchType == TAB.Image) {
            searchLink = "/search-image.html?q=" + encodeURIComponent(val);
        } else if (searchType == TAB.Video) {
            searchLink = "/search-video.html?q=" + encodeURIComponent(val);
        } else if (searchType == TAB.Music) {
            searchLink = "/search-mp3.html?q=" + encodeURIComponent(val);
        } else {
            searchLink = URL[searchType] + "&q=" + encodeURIComponent(val);
            newTab = true;
        }
        
        if (isIndex3) {
            searchLink += "&f=index3";
        }
        
        if (newTab) {
            window.open(searchLink, '_blank');
        } else {
            location.href = searchLink;
        }
    };
    
    var isIndex3 = (location.pathname == "/index3" || lbUtils.getParameterByName("f") == "index3");
    
    var filteSites = function(term) {
        var rs = [];
        
        term = term.toLowerCase();
        var hasDot = (term.indexOf(".") > -1);
        
        if (typeof lbSites === "undefined" || term.length == 0) {
            return rs;
        }
        
        var foundMain = [];
        var foundSub = [];
        
        $.each(lbSites, function() {
            if (hasDot) {
                if (rs.length >= 3) {
                    return false;
                }
                
                if (this[0].indexOf(term) > -1) {
                    rs.push({
                        type: "site",
                        data: this
                    });
                }
            } else {
                if (foundMain.length >= 3) {
                    return false;
                }
                
                var pos = this[1].indexOf(term);
                if (pos > -1) {
                    var lastDotPos = this[1].lastIndexOf(".");
                    if (lastDotPos == -1 && pos == 0) {
                        foundMain.push({
                            type: "site",
                            data: this
                        });
                    } else {
                        foundSub.push({
                            type: "site",
                            data: this
                        });
                    }
                }
            }
        });
        
        if (!hasDot) {
            if (foundMain.length == 3) {
                return foundMain;
            }
            
            var rs = _.union(foundMain, foundSub);
            if (rs.length <= 3 ) {
                return rs;
            }
            
            return rs.splice(0, 3);
        }
        
        return rs;
    };
    
    var term;
    var searchGoogleService = function(type) {
        var serviceUrl = "https://ajax.googleapis.com/ajax/services/search/";
        var webSearchUrl = "http://suggestqueries.google.com/complete/search?client=hp&hl=vi&cp=1&gs_id=c";
        
        var options = _.clone(autoCompleteOptions);
        inputBox.enableAutocomplete(serviceUrl, _.extend(options, {
            activeItemClass: 'autoserfrd_active',
			coverListClass: 'ctnautocomplete',
			listClass: 'autoserfrd',
			maxDisabledTextLength:true,
            makeUrl: function (t) {
                term = t;
                var type = "web";
                switch (searchType) {		
                    case TAB.Image:
                        return "http://suggestqueries.google.com/complete/search?client=img&hl=vi&gs_nf=3&gs_rn=0&gs_ri=img&ds=i&cp=2&gs_id=13&q=" + encodeURIComponent(term);
                    break;
                    case TAB.News:
                        type = "news";
                    break;
                    case TAB.Maps:
                        type = "local";
                    break;
                    case TAB.Video:
                        return "http://suggestqueries.google.com/complete/search?client=video-hp&hl=vi&gs_nf=3&gs_rn=0&gs_ri=video-hp&ds=yt&cp=4&gs_id=k&q=" + encodeURIComponent(term);
                    break;
                    default:
                        return webSearchUrl + "&q=" + encodeURIComponent(term);
                }

                return serviceUrl + type + "?v=1.0&hl=vi&rsz=8&q=" + encodeURIComponent(term);
            },
            parseData: function (e) {
                var sites = [];
                if (searchType == 0 || searchType == TAB.GoogleSearch || searchType == TAB.Image || searchType == TAB.Video) {
                    var a = (searchType == TAB.Image || searchType == TAB.Video) ? [] : filteSites(term);
                    
                    if (e && e[1]) {
                        $.each(e[1], function() {
                            var c = {
                                type: "google",
                                data: [
                                    this[0].replace(/<b>([^<]+)<\/b>/g, "$1"),
                                    this[0].replace(/<b>([^<]+)<\/b>/g, "$1")
                                ]
                            };
                            a.push(c);
                        });
                    }
                    
                    return a;
                }
                
                if (!e) {
                    return null;
                }
                
                var a = new Array();
                e = e.responseData.results;

                $.each(e, function () {
                    var c = {
                        type: "google",
                        data: [
                            this.titleNoFormatting,
                            this.title
                        ]
                    };
                    a.push(c);
                });
                return a;
            },
            formatItem: function (a,b,c) {
                var html = "";
                
                switch (a.type) {
                    case "site":
                        html = "<a href='javascript:;' title='" + a.data[1] + "'><img src='" + LabanConfig.staticIconUrl + '/' + a.data[3] + "' width='16' height='16' /> " + a.data[0] + "</a>";
                        break;
                    case "google":
                        html = "<a href='javascript:;' title='" + a.data[0] + "'>" + a.data[1] + "</a>";
                        break;
                }
                
                return html;
            },
            onItemSelected: function (a) {
                switch (a.type) {
                    case "site":
                        window.open(a.data[2], '_blank');
                        log('', 5, 0, TAB.ZingSearch);
                        break;
                    case "google":
                        doSearch();
                        break;
                }
                
            },
            formatInput: function (a) {
                return a.data[0];
            }
        }));
    };
    
    var mp3Search = function() {
        var mp3Config = {
            baseUrl: 'http://mp3.zing.vn',
            imgUrl: 'http://image.mp3.zdn.vn'
        }
        
        var suggestURL = '/ajax/searchMp3';
        var options = _.clone(autoCompleteOptions);
		inputBox.enableAutocomplete(suggestURL, _.extend(options, {
			itemClass:'zme-autocomplete-item',
			groupClass:'search-item',
			coverClass:'search-autocomplete',
			hasGroup: true,
			makeUrl: function (term){
				return suggestURL + '?q=' + encodeURIComponent(term) + '&callback=?';
			},
			formatItem: function (a,b,c) {
				var str_item = '';
                
                if (c != 'artist') {
                    if (a.name.indexOf(' + ') > -1) {
                        var tmp = a.name.split(' + ');
                        tmp = tmp.splice(0, tmp.length - 1);
                        a.name = tmp.join(' + ');
                    }
                }
                
				switch (c) {
					case 'artist':
						str_item = "<a title='" + a.name + "'  href='javascript:void(0);'>" +
                            "<img  width='35' height='35' class='search-img' alt='" + a.name + "' src='" + mp3Config.imgUrl + "/thumb/94_94/" + a.avatar + "' onerror='this.src=\"" + mp3Config.imgUrl + "/thumb/94_94/avatars/noavatar.gif\"' />" +
                            "<strong>" + a.name + "</strong></a>";
						break;
					case 'album':
						str_item = "<a title='" + a.name + "'  href='javascript:void(0);'>" +
                            "<img width='35' height='35' class='search-img' alt='" + a.name + "' src='" + mp3Config.imgUrl + "/thumb/94_94/" + a.avatar + "' onerror='this.src=\"" + mp3Config.imgUrl + "/thumb/94_94/covers/noavatar.jpg\"' />" + a.name + "<br /><span>" + a.artist + "</span></a>";
						break;
					case 'video':
						str_item =
						"<a title='" + a.name + "' href='javascript:void(0);'>" + 
						"<img width='62' height='35' class='search-img' alt='" + a.name + " /" + a.artist + " '" + 
						" src='http://image.mp3.zdn.vn/thumb/128_72/" + a.avatar + "' onerror=\"this.src='" + mp3Config.imgUrl + "/thumb/128_72/thumb_video/noavatar.jpg'\" />" + a.name + "<br/><span >" + a.artist + "</span>" +
						"</a>";
						break;
					case 'song':
						str_item = "<a title='" + a.name + "'  href='javascript:void(0);'>" + a.name + "<br/><span >" + a.artist + "</span></a>";
						break;

				}
				return str_item;
			},
			onItemSelected: function (a,b,c) {
				var q = encodeURIComponent(inputBox.val());
				var url;
                switch(c){
                    case 'artist':
                        url = mp3Config.baseUrl + "/tim-kiem/bai-hat.html?q=" + q + "&t=artist";
                        break;
                    case 'album':
                        url = mp3Config.baseUrl + "/album/" + q + "/" + a.object_id + ".html";
                        break;
                    case 'video':
                        url = mp3Config.baseUrl + "/video-clip/" + q + "/" + a.object_id + ".html";
                        break;
                    default:
                        url = mp3Config.baseUrl + "/bai-hat/" + q + "/" + a.object_id + ".html";
                        break;
                }
				window.open(url, '_blank');
                
                log('', 5, 0, TAB.Music);
			},
			addHeader: function (d) {
				return "<a target='_blank' class='search-for zme-autocomplete-activeItem' href='"+mp3Config.baseUrl+"/tim-kiem/bai-hat.html?q="+encodeURIComponent(d)+"' title='Tìm kiếm với \""+d+"\"'>Tìm kiếm với \""+d+"\"</a>";
			},
			formatInput: function (a) {
				return a.name;
			}
		}));
        
    };
    
    var refreshMp3Search = function(keyword, times) {
        if (typeof times == "undefined" || times <= 0 || isNaN(times)) {
            times = 1;
        }
        
        if (times > 3) {
            return;
        }
        
        $.get('/search-mp3.html?refresh=1&q=' + encodeURIComponent(keyword), function(resp) {
            if (resp) {
                $("#mp3Result").html(resp);
            } else {
                setTimeout(function(){refreshMp3Search(keyword, ++times);}, 1000);
            }
        });
    };
    this.refreshMp3Search = refreshMp3Search;
    
    var setKeyword = function(keyword) {
        if (typeof keyword == "undefined") {
            keyword = lbUtils.getParameterByName('q');
        }
        
        LabanConfig.searchKeyword = keyword;
    }
    this.setKeyword = setKeyword;
    
    var init = function() {
        setKeyword();
        
        var tabs = searchBox.find("li a");
        tabs.click(function(e) {
            var type = parseInt($(this).attr('rel'));
            if (!type || isNaN(type)) {
                return;
            }
            
            if (type == TAB.Question) {
                log('', 5, 0, TAB.Question);
                return;
            }
            
            e.preventDefault();
            
            searchBox.find("li.select").removeClass("select");
            $(this).parent().addClass("select");
            
            searchType = type;
            
            var defaultText = DEFAULT_TEXT[type];
            var cssClass = CSS_CLASS[type];
            
            inputBox.disableAutocomplete();
            
            if (typeof LabanConfig.searchKeyword != "undefined" && LabanConfig.searchKeyword != null && LabanConfig.searchKeyword.length > 0) {
                inputBox.val(LabanConfig.searchKeyword);
                currentKeyword = LabanConfig.searchKeyword;
                LabanConfig.searchKeyword = "";
            } else if (currentKeyword != "") {
                inputBox.val(currentKeyword);
                if (LabanConfig.searchWhenChangeTab) {
                    doSearch();
                }
            } else {
                inputBox.val(defaultText);
            }
            if (inputBox.attr('class').indexOf('inp_box') > -1) {
                inputBox.attr('class', 'inp_box ' + cssClass);
            } else {
                inputBox.attr('class', 'google_box');
            }
            
            switch (type) {
                case TAB.Music:
                    mp3Search();
                break;
                case TAB.GoogleSearch:
                case TAB.Image:
                case TAB.News:
                case TAB.Maps:
                case TAB.Video:
                    searchGoogleService(type);
                break;
            }
            
            
        });
        
        if (typeof LabanConfig.defaultSearchTab != "undefined") {
            searchBox.find("a[rel='"+ LabanConfig.defaultSearchTab +"']").click();
        } else {
            tabs.get(0).click();
        }
        
        inputBox.focus(onFocus).blur(onBlur).keypress(onKeyPress);
        
        $("#btnSearch").click(doSearch);
        
        if (LabanConfig.pageId == 1) {
            inputBox.focus();
            onFocus();
        }
    };
    this.init = init;
}();
