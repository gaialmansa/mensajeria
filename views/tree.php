<!--

/
    usr
    bin
    var
        log
        cache
    home




⏵
-->

<div class="zbfBox zbfExpandBox">
    <h1>Tree dev</h1>

    <div class="zjTreeContainer">

        <div class="zjTreeNode">
            <div class="zjTreeHeader">
                <div class="zjTreeIcon">
                    <img src="/res/zaf/zth1/icon/music.svg" height="24px" width="24px"/>
                </div>
                <div class="zjTreeLabel">/</div>
                <div class="zjTreeInfo">No-se-cuántos items</div>
                <div class="zjTreeControl"><a href="javascript:void(0)">⏷</a>
                </div>
            </div>
            <div class="zjTreeSubtree">
                <div class="zjTreeNode">
                    <div class="zjTreeHeader">
                        <div class="zjTreeIcon">
                            <img src="/res/zaf/zth1/icon/music.svg" height="24px" width="24px"/>
                        </div>
                        <div class="zjTreeLabel">usr</div>
                    </div>
                    <div class="zjTreeContent">Suspendisse ultricies in quam
                        non pretium. In sit amet lectus dapibus, sollicitudin
                        dui non, finibus felis. Mauris consequat lobortis nisi,
                        nec congue magna lobortis ac. Praesent eget ligula in
                        nibh posuere sodales. Vivamus tincidunt, mauris nec
                        suscipit mattis, turpis nisi cursus dolor, non mattis
                        dolor massa sed leo. Fusce accumsan quam vel turpis
                        blandit egestas. Aliquam erat volutpat. Nulla vel mollis
                        nulla. Quisque ac pulvinar tortor. Pellentesque
                        malesuada nec massa id eleifend. Nullam varius a odio ac
                        pulvinar. Cras condimentum ligula vitae leo consectetur
                        fermentum. Duis efficitur tellus eget imperdiet
                        consectetur.
                    </div>
                </div>
                <div class="zjTreeNode">
                    <div class="zjTreeHeader">
                        <div class="zjTreeIcon">
                            <img src="/res/zaf/zth1/icon/music.svg" height="24px" width="24px"/>
                        </div>
                        <div class="zjTreeLabel">bin</div>
                    </div>
                </div>
                <div class="zjTreeNode">
                    <div class="zjTreeHeader">
                        <div class="zjTreeIcon">
                            <img src="/res/zaf/zth1/icon/music.svg" height="24px" width="24px"/>
                        </div>
                        <div class="zjTreeLabel">var</div>
                        <div class="zjTreeControl">
                            <a href="javascript:void(0)">⏵</a></div>
                    </div>
                    <div class="zjTreeSubtree zjTreeClosed">
                        <div class="zjTreeNode">
                            <div class="zjTreeHeader">
                                <div class="zjTreeIcon">
                                    <img src="/res/zaf/zth1/icon/music.svg" height="24px" width="24px"/>
                                </div>
                                <div class="zjTreeLabel">log</div>
                            </div>
                            <div class="zjTreeContent">Suspendisse ultricies in quam
                                non pretium. In sit amet lectus dapibus, sollicitudin
                                dui non, finibus felis. Mauris consequat lobortis nisi,
                                nec congue magna lobortis ac. Praesent eget ligula in
                                nibh posuere sodales. Vivamus tincidunt, mauris nec
                                suscipit mattis, turpis nisi cursus dolor, non mattis
                                dolor massa sed leo. Fusce accumsan quam vel turpis
                                blandit egestas. Aliquam erat volutpat. Nulla vel mollis
                                nulla. Quisque ac pulvinar tortor. Pellentesque
                                malesuada nec massa id eleifend. Nullam varius a odio ac
                                pulvinar. Cras condimentum ligula vitae leo consectetur
                                fermentum. Duis efficitur tellus eget imperdiet
                                consectetur.
                            </div>
                        </div>
                        <div class="zjTreeNode">
                            <div class="zjTreeHeader">
                                <div class="zjTreeIcon">
                                    <img src="/res/zaf/zth1/icon/music.svg" height="24px" width="24px"/>
                                </div>
                                <div class="zjTreeLabel">cache</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="zjTreeNode">
                    <div class="zjTreeHeader">
                        <div class="zjTreeIcon">
                            <img src="/res/zaf/zth1/icon/ball-pen-fill.svg" height="24px" width="24px"/>
                        </div>
                        <div class="zjTreeLabel">home</div>
                    </div>
                </div>
            </div>
        </div>


    </div><!-- TreeContainer-->

    <script>
        document.addEventListener('click', function (event) {
            const target = event.target;

            if (target.matches('.zjTreeControl a')) {
                const subtree = target.closest(".zjTreeNode").querySelector(".zjTreeSubtree");

                if (subtree.classList.contains("zjTreeClosed")) {
                    subtree.classList.remove("zjTreeClosed");
                    target.innerHTML = "⏷";
                } else {
                    subtree.classList.add("zjTreeClosed");
                    target.innerHTML = "⏵";
                }
            }
        });

    </script>

</div>
