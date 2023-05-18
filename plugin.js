/* global tinymce, twemoji, _wpemojiSettings, _smileySettings */


(function () {

    //经典编辑器
    wp.domReady(function () {
        function getHtml() {

            let json_data = window.easy_emoji_json_data;

            window.easy_emoji_switch = function (elementID, listName, n) {
                var elem = document.getElementById(elementID);
                var elemlist = elem.getElementsByTagName("li");
                for (var i = 0; i < elemlist.length; i++) {
                    elemlist[i].className = "normal";
                    var m = i + 1;
                    document.getElementById(listName + "_" + m).className = "normal";
                }
                elemlist[n - 1].className = "current";
                document.getElementById(listName + "_" + n).className = "current";
            }

            let tabBarString = "";
            {
                let tagNameStr = "";
                {
                    let index = 1;
                    for (key in json_data) {
                        if (index == 1) {
                            tagNameStr += `        <li  class="current" onmouseover="easy_emoji_switch('hotnews_caption','list',${index++});">${key}</li>`;
                        } else {
                            tagNameStr += `        <li  class="normal" onmouseover="easy_emoji_switch('hotnews_caption','list',${index++});">${key}</li>`;
                        }
                        tagNameStr += "\n"
                    }
                }
                tabBarString = `
                <div id="hotnews_caption">
                    <ul style="margin-left:0px;">
                ${tagNameStr}
                    </ul>
                </div>
            `
            }

            let tabContent = '<div id="hotnews_content">\n'
            {
                let tabContentItems = "";
                let index = 1;
                for (key in json_data) {
                    let beginStr = "";
                    if (index == 1) {
                        beginStr = `<div class="current" id="list_${index++}">`
                    } else {
                        beginStr = `<div class="normal" id="list_${index++}">`
                    }
                    beginStr += "\n";

                    let item = json_data[key]

                    for (imgname in item.names) {
                        //let imgStr = `<div class="IMG_PARENT"> <img src="${item.url + item.names[imgname]}"> </div>`
                        let imgStr = `<img src="${item.url + item.names[imgname]}" >`
                        beginStr = beginStr + "\t" + imgStr + "\n"
                    }

                    beginStr += "</div>";

                    tabContentItems += beginStr
                    tabContentItems += "\n"
                }
                tabContent += tabContentItems;
                tabContent += '\n'
            }

            let curHtml = `
        <div id="easy_emoji">
            ${tabBarString}
            ${tabContent}
        </div>
        `

            return curHtml;
        }
        tinymce.PluginManager.add('easy_emoji', function (editor) {
            editor.addButton('easy_emoji', {
                type: 'panelbutton',
                panel: {
                    classes: 'easy-panel',
                    role: 'application',
                    autohide: true,
                    html: getHtml,
                    onShow: function (e) {
                        let id = this._id;

                        //需要删除这个面板高度
                        let resetHeigth;
                        resetHeigth = function () {
                            let ok = false;
                            do {
                                let ele = document.getElementById(id)
                                if (!ele) break;
                                if (ele.style.height == "") break;
                                ele.style.height = ""
                                ele.style.width = ""
                                ok = true;
                            } while (false)

                            if (!ok) setTimeout(resetHeigth, 10);
                        }
                        resetHeigth();
                    },
                    onclick: function (e) {
                        var img = e.target;
                        if (img && img.tagName == "IMG") {
                            var imgHtml = "<img width=48 height=48 src='" + img.currentSrc + "'>"
                            editor.insertContent(imgHtml);
                        }

                        this.hide();
                        //debugger;
                    }
                },
                tooltip: 'Emoticons'
            });
        });
    });


    //新版块编辑器
    let plugin_name = "easy/emoji";

    const { registerFormatType, unregisterFormatType, toggleFormat, applyFormat } = window.wp.richText;
    const { BlockControls } = wp.blockEditor;
    const { ToolbarGroup, ToolbarButton } = wp.components;
    const { registerBlockType } = wp.blocks;

    unregisterFormatType(plugin_name);

    const buttonEasyEmoji = function ({ isActive, onChange, value }) {
        const onClick = function () {
            alert("还没写完...")
            debugger;
            onChange(
                wp.richText.insert(value, "x")
            );
        };
        return wp.element.createElement(
            BlockControls,
            null,
            wp.element.createElement(
                ToolbarGroup,
                null,
                wp.element.createElement(ToolbarButton, {
                    icon: "easy_emoji",
                    title: "Insert custom HTML",
                    onClick: onClick,
                    isActive: isActive
                })
            )
        );
    };

    registerFormatType(plugin_name, {
        title: "Sample output",
        tagName: "samp",
        className: null,
        edit: buttonEasyEmoji
    });

})();