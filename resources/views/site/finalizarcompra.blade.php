@extends("site.layout")

@section("title", "Finalizar Pedido")

@push('styles')
    
@endpush
    <link rel="stylesheet" href="{{ asset("css/site/finalizarcompra.css") }}">
@section("content")

<div class="container">
    <form action="{{ route("site.addpedido") }}" method="POST">
        @csrf

        <input id="secret_token" type="hidden" value="{{ Str::random(20) }}" name="secret_token">

        <h1 id="title">Finalize seu pedido:</h1>
        
        <section class="stage-section stage-active">
            <p class="stage-title">Forma de recebimento:</p>
            <div class="stage-content">
                <div class="stage-innercontent">
                    <fieldset class="stage-fieldset" disabled>
                        <div>
                            <input id="entrega" type="radio" value="entrega" data-tipo="entrega" name="forma">
                            <label for="entrega">Entrega (com montagem)</label>
                        </div>
                        <div>
                            <input id="retirada" type="radio" value="retirada" data-tipo="retirada" name="forma">
                            <label for="retirada">Retirada</label>
                        </div>
                    </fieldset>
                </div>
                <button class="stage-btn" type="button" disabled>Confirmar forma acima</button>
            </div>
        </section>

        <section class="stage-section">
            <p class="stage-title">Endereço:</p>
            <div class="stage-content">
                <div class="stage-innercontent stage-primary-innercontent">
                    <fieldset class="stage-fieldset" data-tipo="entrega">
                        @foreach ($enderecos as $endereco)
                            <div>
                                <input id="endereco{{ $endereco->id }}" type="radio" value="{{ $endereco->id }}" name="endereco_entrega">
                                <label for="endereco{{ $endereco->id }}">
                                    {{ format_endereco($endereco) }}
                                </label>
                            </div>
                        @endforeach
                        <a href="{{ route("site.perfil") }}#endereco"><i class="fa-solid fa-plus"></i> Adicionar outro endereço</a>
                        {{-- @include("site.modals.addmodal", ["id" => $endereco->id]) --}}
                    </fieldset>
                </div>
                <div class="stage-innercontent stage-secondary-innercontent">
                    <fieldset class="stage-fieldset" data-tipo="retirada">
                        @foreach ($enderecos_retirada as $endereco_retirada)
                            <div>
                                <input id="endereco_retirada" type="radio" value="{{ $endereco_retirada->id }}" name="endereco_retirada">
                                <label for="endereco_retirada">
                                    {{ format_endereco($endereco_retirada) }}
                                </label>
                            </div>
                        @endforeach
                    </fieldset>
                </div>
                <button class="stage-btn" type="button" disabled>Confirmar endereço acima</button>
            </div>
        </section>

        <section class="stage-section">
            <p class="stage-title">Informações:</p>
            <div class="stage-content">
                <div class="stage-innercontent stage-multipleinnercontent" data-tipo="retirada">
                    <div>
                        <label>Data de INÍCIO do evento:</label>
                        <input id="date" class="date" type="date" name="date" required>
                    </div>
                    <div>
                        <label>Horário de INÍCIO (8h - 18h):</label>
                        <input id="time" class="time" type="time" name="time" min="08:00" max="18:00" required> 
                    </div>
                    <div>
                        <label>Data de FIM do evento:</label>
                        <input id="date2" class="date" type="date" name="date2" required>
                    </div>
                    <div>
                        <label>Horário de FIM (8h - 18h):</label>
                        <input id="time2" class="time" type="time" name="time2" min="08:00" max="18:00" required> 
                    </div>
                    <div id="tax">
                        <label> Frete: </label> <div id="tax-text"></div>
                    </div>
                </div>
                <button class="stage-btn" type="button" disabled>Confirmar data acima</button>
            </div>
        </section>

        <section class="stage-section">
            <p class="stage-title">Método de pagamento:</p>
            <div class="stage-content">
                <div class="stage-innercontent">
                    <fieldset class="stage-fieldset">
                        {{-- <div>
                            <input id="pix" type="radio" value="pix" name="pagamento">
                            <label for="pix">Pix</label>
                        </div>
                        <div>
                            <input id="cc" type="radio" value="pix" name="pagamento">
                            <label for="cc">Cartão de Crédito</label>
                        </div>
                        <div>
                            <input id="cd" type="radio" value="pix" name="pagamento">
                            <label for="cd">Cartão de Débito</label>
                        </div>
                        <div>
                            <input id="boleto" type="radio" value="pix" name="pagamento">
                            <label for="boleto">Boleto</label>
                        </div> --}}
                        <div>
                            <input id="c" type="radio" value="c" name="pagamento">
                            <label for="c">Cartão de Crédito/Débito</label>
                        </div>
                        <div>
                            <input id="pix" type="radio" value="pix" name="pagamento">
                            <label for="pix">Pix</label>
                        </div>
                    </fieldset>
                </div>
                <button class="stage-btn" type="button" disabled>Confirmar forma de pagamento acima</button>
            </div>
        </section>

        <section class="stage-section">
            <p class="stage-title">Revisar pedido:</p>
            <div class="stage-content">
                <div class="stage-innercontent">
                    <div id="stage-details">
                        <span id="stage-details-pedido">
                        </span>
                        Produtos:
                        @foreach ($items as $item)
                        <div class="stage-details-item">
                            <img class="satage-details-item-img" src="{{ $item['produto']['img'] }}">
                            <p>{{ $item['nome'] }} <span class="light-text">Preço: R$ {{ number_format($item['valor'], 2, ",", ".") }} ({{ $item['quantidade'] }} x R$ {{ number_format($item['produto']['valor'], 2, ",", ".") }})</span></p>
                        </div>
                        @endforeach
                        <p id="stage-details-add">Adicional de montagem: <span class="light-text">R$ {{ number_format($adicional_montagem, 2, ",", ".") }}</span></p>

                        <p id="stage-details-tax">Taxa de entrega: <span id="stage-details-tax-text" class="light-text"></span></p>
                        <p id="stage-details-total">Total: <span id="stage-details-total-text" class="light-text"></span></p>
                    </div>
                    <div id="obs-div">
                        <label for="observacao">Observação:</label>
                        <textarea id="observacao" type="text" name="obs" placeholder="Digite aqui sua observação." rows="2" cols="20"></textarea>
                    </div>
                </div>
                <button id="finalizar-btn" type="submit">Finalizar Pedido</button>
            </div>
        </section>

    </form>
    
</div>

@endsection

@push("scripts")
    <script>
        $(document).ready(function() {
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                event.preventDefault();
                return false;
                }
            });
    });
    </script>
    
    <script>
        $(document).ready(() => {
            let first_fieldset = $($($(".stage-section")[0]).find(".stage-fieldset"))[0];        
            $(first_fieldset).prop("disabled", false);
        })

        setWithdrawlDateLimit();

        var pedido = {};
        var unlocked_stage = 0;
        var active_stage = 0;
        var active_stage_section_el = $(".stage-section")[active_stage];
        var new_date;


        function activateStage() {
            
            let stage_sections = $(".stage-section");

            if (unlocked_stage < active_stage) {
                unlocked_stage = active_stage;
            }

            for (i = 0; i <= stage_sections.length; i++) {
                if (i == (active_stage)) {
                    active_stage_section_el = $(stage_sections[i]);
                    $(stage_sections[i]).addClass("stage-active");
                }
                else {
                    $(stage_sections[i]).removeClass("stage-active");
                }
                if (i < active_stage) {
                    $(stage_sections[i]).addClass("stage-returnable");
                }
            }

            checkUnlockedStages();
        }

        function checkUnlockedStages() {
            let stage_sections = $(".stage-section");

            for (i = 0; i <= stage_sections.length; i++) {
                if (i <= unlocked_stage) {
                    $(stage_sections[i]).addClass("stage-returnable");
                } else {
                    if (new_date && $(stage_sections[i]).find("input[type=date]")) {

                    } else {
                        $(stage_sections[i]).removeClass("stage-returnable");
                        $($(stage_sections[i]).find(".stage-btn")[0]).prop('disabled', true);
                        $(stage_sections[i]).find("input:not([type=radio]), textarea").each(function() {
                            $(this).val("");
                        });
                        $(stage_sections[i]).find("input[type=radio]").each(function() {
                            $(this).prop('checked', false);
                        });
                    }
                }
            }
        }

        window.onload = function() {
            // second_section = $(".stage-section")[1];
            // secondary_innercontent = $(second_section).find(".stage-secondary-innercontent");
            // secondary_innercontent.hide();

            checkFilled(0);
            // $('input, textarea').each(function() {
            //     $(this).val("");
            // });
            // $('input:checkbox').removeAttr('checked');
            // $('input[name="correctAnswer"]').attr('checked', false);
        }

        $(".stage-btn").on("click", function() {
            let clicked_btn = $(this)[0];

            let stage_btns = $(".stage-btn");
            
            for (i = 0; i < stage_btns.length; i++) {
                if (clicked_btn == stage_btns[i]) {
                    var clicked_index = i;
                }
            }

            
            active_stage = clicked_index + 1;
            activateStage(active_stage);

            if (checkFilled(active_stage)) {
                $($(active_stage_section_el).find(".stage-btn")[0]).prop('disabled', false);
            }

            buildPedido(clicked_index);

        });

        $(".stage-section").on("click", function(e) {
            let clicked_section = $(this)[0];
            
            if (e.target == $(clicked_section).find(".stage-btn")[0] || !$(clicked_section).hasClass("stage-returnable")) {
                e.stopPropagation();
                return;
            }

            stage_sections = $(".stage-section");

            for (i = 0; i < stage_sections.length; i++) {
                if (clicked_section == stage_sections[i]) {
                    var clicked_index = i;
                }
            }

            active_stage = clicked_index;

            activateStage(active_stage);
        });

        $(".stage-content input").each(function () {
            $(this).on("change", () => {
                if (checkFilled(active_stage)) {
                    $($(active_stage_section_el).find(".stage-btn")[0]).prop('disabled', false);
                }
            })
        })

        function buildPedido(stage) {
            switch (stage) {
                case 0:
                    let forma_input = $($($(".stage-section")[0]).find("input[type=radio]:checked"))[0];
                    let forma_label = $("label[for='" + $(forma_input).attr('id') + "']");
                    let forma = forma_label.text().trim();
                    pedido['forma'] = forma;
                    break;
                case 1:
                    let end_input = $($($(".stage-section")[1]).find("input[type=radio]:checked"))[0];
                    let end_label = $("label[for='" + $(end_input).attr('id') + "']");
                    let end = end_label.text().trim();
                    pedido['end'] = end;
                    break;
                case 2:
                    let date_val = $($($($(".stage-section")[2]).find("input[name=date]"))[0]).val();
                    let date_split = date_val.split("-");
                    let date_val2 = $($($($(".stage-section")[2]).find("input[name=date2]"))[0]).val();
                    let date_split2 = date_val2.split("-");

                    let time_val = $($($($(".stage-section")[2]).find("input[name=time]"))[0]).val();
                    let time_val2 = $($($($(".stage-section")[2]).find("input[name=time2]"))[0]).val();

                    let formatted_date = "De " + date_split[2]+"/"+date_split[1]+"/"+date_split[0] + " às " + time_val + " até " +  date_split2[2]+"/"+date_split2[1]+"/"+date_split2[0] + " às " + time_val2;

                    pedido['date'] = formatted_date;
                    break;
                case 3:
                    let pagamento_input = $($($(".stage-section")[3]).find("input[type=radio]:checked"))[0];
                    let pagamento_label = $("label[for='" + $(pagamento_input).attr('id') + "']");
                    let pagamento = pagamento_label.text().trim();
                    pedido['pagamento'] = pagamento;
                    let pedido_text =
                    `<p>Forma: <span class="light-text">${pedido['forma']}</span></p>
                    <p>Endereço: <span class="light-text">${pedido['end']}</span></p>
                    <p>Data do evento: <span class="light-text">${pedido['date']}</span></p>
                    <p>Método de pagamento: <span class="light-text">${pedido['pagamento']}</span></p>`;

                    {{-- <p>Data de entrega: <span class="light-text">${pedido['date']}</span></p> --}}


                    let stage_details = $($(".stage-section")[4]).find("#stage-details")[0];
                    let stage_details_pedido = $("#stage-details-pedido");
                    $(stage_details_pedido).html(pedido_text);
                    break;
            }
            switch (stage) {
                case 0:
                    let forma_input = $($($(".stage-section")[0]).find("input[type=radio]:checked"))[0];
                    let forma_label = $("label[for='" + $(forma_input).attr('id') + "']");
                    let forma = forma_label.text().trim();
                    pedido['forma'] = forma;
                    break;
                case 1:
                    let end_input = $($($(".stage-section")[1]).find("input[type=radio]:checked"))[0];
                    let end_label = $("label[for='" + $(end_input).attr('id') + "']");
                    let end = end_label.text().trim();
                    pedido['end'] = end;
                    break;
                case 2:
                    let date_val = $($($($(".stage-section")[2]).find("input[name=date]"))[0]).val();
                    let date_split = date_val.split("-");
                    let date_val2 = $($($($(".stage-section")[2]).find("input[name=date2]"))[0]).val();
                    let date_split2 = date_val2.split("-");

                    let time_val = $($($($(".stage-section")[2]).find("input[name=time]"))[0]).val();
                    let time_val2 = $($($($(".stage-section")[2]).find("input[name=time2]"))[0]).val();

                    let formatted_date = "De " + date_split[2]+"/"+date_split[1]+"/"+date_split[0] + " às " + time_val + " até " +  date_split2[2]+"/"+date_split2[1]+"/"+date_split2[0] + " às " + time_val2;

                    pedido['date'] = formatted_date;
                    break;
                case 3:
                    let pagamento_input = $($($(".stage-section")[3]).find("input[type=radio]:checked"))[0];
                    let pagamento_label = $("label[for='" + $(pagamento_input).attr('id') + "']");
                    let pagamento = pagamento_label.text().trim();
                    pedido['pagamento'] = pagamento;
                    let pedido_text =
                    `<p>Forma: <span class="light-text">${pedido['forma']}</span></p>
                    <p>Endereço: <span class="light-text">${pedido['end']}</span></p>
                    <p>Data do evento: <span class="light-text">${pedido['date']}</span></p>
                    <p>Método de pagamento: <span class="light-text">${pedido['pagamento']}</span></p>`;

                    {{-- <p>Data de entrega: <span class="light-text">${pedido['date']}</span></p> --}}


                    let stage_details = $($(".stage-section")[4]).find("#stage-details")[0];
                    let stage_details_pedido = $("#stage-details-pedido");
                    $(stage_details_pedido).html(pedido_text);
                    break;
            }
        }

        function checkFilled(stage) {

            let checked = false;

            switch (stage) {
                case 0:
                    radio_inputs = $($(".stage-content")[0]).find("input[type=radio]");
                    $(radio_inputs).each(function () {
                        if ($(this).is(':checked')) {
                            checked = true;

                            second_section = $(".stage-section")[1];
                            primary_innercontent = $(second_section).find(".stage-primary-innercontent");
                            secondary_innercontent = $(second_section).find(".stage-secondary-innercontent");
                            second_section_title = $(second_section).find(".stage-title");

                            third_section = $(".stage-section")[2];
                            // primary_innercontent = $(third_section).find(".stage-primary-innercontent");
                            // secondary_innercontent = $(third_section).find(".stage-secondary-innercontent");
                            third_section_title = $(third_section).find(".stage-title");

                            if($(this).attr("data-tipo") == "retirada") {
                                second_section_title.text("Endereço da retirada:");
                                primary_innercontent.hide();
                                secondary_innercontent.show();

                                third_section_title.text("Informações do evento (retirada):");
                                $($(third_section).find("input[type='date']")[0]).val("");
                                $($(third_section).find("input[type='date']")[0]).prop("disabled", false);

                                $($(second_section).find("*[data-tipo='entrega']")[0]).find("input").each(function() {
                                    $(this).prop("checked", false);
                                });

                                $($(third_section).find("#tax")[0]).hide();
                                $("#stage-details-tax").hide();

                                $("#stage-details-add").hide();
                                $("#stage-details-total").find("#stage-details-total-text").text("R$ {{ number_format($total, 2, ",", ".") }}");

                                new_date = null;
                                
                                unlocked_stage = 0;
                                checkUnlockedStages();
                            } else {
                                second_section_title.text("Endereço do evento:");
                                primary_innercontent.show();
                                secondary_innercontent.hide();

                                third_section_title.text("informações do evento (entrega):");
                                $($(third_section).find("input[type='date']")[0]).val("");

                                {{-- setNewDate(); --}}
                                setShippinhDateLimit();

                                $($(second_section).find("*[data-tipo='retirada']")[0]).find("input").each(function() {
                                    $(this).prop("checked", false);
                                });
                                

                                unlocked_stage = 0;
                                checkUnlockedStages();
                            }
                        }
                    })
                    break;
                    
                case 1:
                    radio_inputs = $($(".stage-content")[1]).find("input[type=radio]");
                    $(radio_inputs).each(function () {
                        if ($(this).is(':checked')) {
                            checked = true;
                            setNewTax();
                        }
                    })

                    break;

                case 2:
                    date_input = $($(".stage-content")[2]).find("input[name=date]");
                    date_input2 = $($(".stage-content")[2]).find("input[name=date2]");
                    time_input = $($(".stage-content")[2]).find("input[name=time]");
                    time_input2 = $($(".stage-content")[2]).find("input[name=time2]");
                    if ($(date_input).val() && $(date_input2).val() && $(time_input).val() && $(time_input2).val()) {
                        checked = true;
                    }

                    break;

                case 3:
                    radio_inputs = $($(".stage-content")[3]).find("input[type=radio]");
                    $(radio_inputs).each(function () {
                        if ($(this).is(':checked')) {
                            checked = true;
                        } 
                    })
                    break;
                        
                default:
                    return false;
                }

            return checked ? true : false;
        }

        async function setNewTax() {
            const formatNumber = new Intl.NumberFormat()

            $($(third_section).find("#tax-text")[0]).text("calculando...");
            $(".stage-fieldset[data-tipo=entrega]").find("input").each(async function() {
                if ($(this).is(":checked")) {
                    let response = await fetch("{{ route("api.getshippingtax") }}", {
                        "method": "POST",
                        headers: {
                            "X-CSRF-Token": "{{ csrf_token() }}",
                        },
                        body: JSON.stringify({
                            "secret_token": $("#secret_token").val(),
                            "id_end": $(this).val()
                        })
                    });
                    let new_tax = await response.text();
                    new_tax = parseFloat(new_tax);

                    let total = {{ $total + $adicional_montagem }};
                    
                    $($(third_section).find("#tax-text")[0]).text("R$ " + number_format(new_tax, 2, ",", "."));

                    $("#stage-details-tax").find("#stage-details-tax-text").text("R$ " + number_format(new_tax, 2, ",", "."));
                    $("#stage-details-tax").show();

                    $($(third_section).find("#tax")[0]).css("display", "flex");

                    $("#stage-details-total").find("#stage-details-total-text").text("R$ " + number_format((total + new_tax), 2, ",", "."));
                    $("#stage-details-add").show();
                }
            });
        }

        async function setShippinhDateLimit() {
            let response = await fetch("{{ route("api.getshippingdate") }}", {
                "method": "POST",
                headers: {
                    "X-CSRF-Token": "{{ csrf_token() }}",
                },
                body: JSON.stringify({
                    "secret_token": $("#secret_token").val()
                })
            });
            date_limit = await response.text();

            $("#date").attr("min", date_limit);
        }

        async function setWithdrawlDateLimit() {
            let response = await fetch("{{ route("api.getwithdrawaldate") }}", {
                "method": "POST",
                headers: {
                    "X-CSRF-Token": "{{ csrf_token() }}",
                },
                body: JSON.stringify({
                    "secret_token": $("#secret_token").val()
                })
            });
            date_limit = await response.text();

            $("#date").attr("min", date_limit);
        }

        function checkDateRange() {

            let date = $("#date").val();
            let newDate = new Date(date);
            newDate.setHours(newDate.getHours() + 3);
            newDate.setDate(newDate.getDate() + 1);
            $("#date2").attr("min", newDate.toISOString().split('T', 1)[0]);
            newDate.setDate(newDate.getDate() + 7).toLocaleString("en-US", { month: "short", timeZone: 'UTC' });
            $("#date2").attr("max", newDate.toISOString().split('T', 1)[0]);


            if( (new Date($("#date").val()).getTime() >= new Date($("#date2").val()).getTime()))
            {
                $("#date2").val("");
            }
        }

        function checkStage2() {
            date_input = $($(".stage-content")[2]).find("input[name=date]");
            date_input2 = $($(".stage-content")[2]).find("input[name=date2]");
            time_input = $($(".stage-content")[2]).find("input[name=time]");
            time_input2 = $($(".stage-content")[2]).find("input[name=time2]");
            if (!($(date_input).val() && $(date_input2).val() && $(time_input).val() && $(time_input2).val())) {
                $($($(".stage-content")[2]).find(".stage-btn")).prop("disabled", true);
            }
        }

        $("#date2").attr("min", new Date().toISOString().split("T")[0])
        $("#date2").attr("max", new Date().toISOString().split("T")[0])

        $("#date").on("change", () => {
            checkDateRange();
            checkStage2();
        });

        $("#date2").on("change", () => {
            if (/^[1-9]{1}\d{3}-\d{2}-\d{2}$/.test($("#date2").val())) {
                checkDateRange();
                checkStage2();
            }
        });

        $("#time").on("change", () => {
            checkStage2();
        });

        $("#time2").on("change", () => {
            checkStage2();
        });


    </script>
@endpush
