/*!
 * Copyright 2022, KalipsoCollective
 * Released under the MIT License
 * {@link https://github.com/KalipsoCollective/KalipsoTable GitHub}
 * Inspired by jQuery.DataTables
 */


class KalipsoTable {

  /**
   * Prepare and start
   * @param object options 
   * @return void
   */
  constructor(options) {

    this.version = '0.8.0';
    this.loading = false;
    this.result = [];
    this.server = false;
    this.selector = null;
    this.parent = null;
    this.customize = null;
    this.total = 0;
    this.current = 0;
    this.totalPage = 1;
    this.page = 1;
    this.searchParams = {};
    this.search = "";
    this.listeners = [];


    if (window.KalipsoTable === undefined) {
      window.KalipsoTable = {}
    }

    if (window.KalipsoTable.languages === undefined) {
      window.KalipsoTable.languages = {}
    }

    window.KalipsoTable.languages["en"] = {
      "init_option_error": "KalipsoTable cannot be initialized without default options!",
      "target_selector_not_found": "Target selector not found!",
      "server_response_problem": "There was a problem with the server response!",

      "all": "All",
      "sorting_asc": "Sorting (A-Z)",
      "sorting_desc": "Sorting (Z-A)",
      "no_record": "No record!",
      "out_of_x_records": "out of [X] records",
      "showing_x_records_from_y_records": "Showing [X] records from [Y].",
      "prev": "Previous",
      "next": "Next",
      "first": "First",
      "last": "Last",
      "search": "search",
    }

    let defaultOptions = {
      language: "en",
      server: false,
      schema: '<div class="table-row">' +
        '<div class="column-25">[L]</div>' + // Listing option select
        '<div class="column-25">[S]</div>' + // Full search input
        '<div class="column-100">[T]</div>' + // Table
        '<div class="column-50">[I]</div>' + // Info
        '<div class="column-50">[P]</div>' + // Pagination
        '</div>',
      columns: [],
      order: ["id", "asc"],
      source: [], // object or string(url)
      lengthOptions: [
        {
          "name": "5",
          "value": 5,
          "default": true
        },
        {
          "name": "10",
          "value": 10,
        },
        {
          "name": "50",
          "value": 50,
        },
        {
          "name": "100",
          "value": 100
        },
        {
          "name": '[ALL]',
          "value": 0,
        }
      ],
      selector: null,
      tableHeader: {
        searchBar: true
      },
      customize: {
        tableWrapClass: null,
        tableClass: null,
        tableHeadClass: null,
        tableBodyClass: null,
        tableFooterClass: null,
        inputClass: null,
        selectClass: null,
        paginationUlClass: null,
        paginationLiClass: null,
        paginationAClass: null,
      },
      tableFooter: {
        visible: false,
        searchBar: true
      },
      params: {},
      length: 10,
      fullSearch: true
    }

    this.bomb(this.version, "debug");

    if (typeof options === 'object') {

      this.options = this.mergeObject(defaultOptions, options);
      this.server = this.options.server;
      this.selector = this.options.selector;
      this.customize = this.options.customize;

      if (this.selector !== null && document.querySelector(this.selector)) {
        this.parent = document.querySelector(this.options.selector);
        this.parent.classList.add('kt-wrapper');
        this.init();
      } else {
        this.bomb(this.l10n("target_selector_not_found") + ' (' + this.options.selector + ')', "debug");
      }

    } else {

      this.bomb(this.l10n("init_option_error"), "error");
    }
  }

  // 
  /**
   * Provides synchronization of setting data.
   * @param object defaultObj  main object data
   * @param object overridedObj  overrided object data
   * @return object
   */
  mergeObject(defaultObj, overridedObj) {
    let that = this
    Object.keys(defaultObj).forEach(function (key) {
        if (overridedObj.hasOwnProperty(key) && typeof overridedObj[key] === "object" && !(overridedObj[key] instanceof Array)) {
          that.mergeObject(defaultObj[key], overridedObj[key]);
        } else if (!overridedObj.hasOwnProperty(key)) {
          overridedObj[key] = defaultObj[key];
        }
    });
    return overridedObj;
  }

  /**
   * Returns translation using key according to active language.
   * @param string key   language key  
   * @return string
   */
  l10n(key) {

    const dir = this.options !== undefined ? this.options.language : "en"

    if (window.KalipsoTable.languages[dir] === undefined) {
      this.bomb("Language definitions not found for " + dir, "error")
    }

    if (window.KalipsoTable.languages[dir][key] !== undefined) {
      return window.KalipsoTable.languages[dir][key]
    } else {
      return key
    }
  }

  /**
   * It sends an output to the console by attributes.
   * @param string warn  console message
   * @param string type  console message type
   * @return void
   */
  bomb(warn, type = "log") {

    warn = "KalipsoTable: " + warn
    switch (type) {

      case "debug":
        console.debug(warn)
        break;

      case "warning":
        console.warn(warn)
        break;

      case "error":
        console.error(warn)
        break;

      case "info":
        console.info(warn)
        break;

      default:
        console.log(warn)
        break;
    }
  }

  /**
   * The table structure is created.
   * @return void
   */
  async init() {

    const sorting = this.sortingSelect();
    const fullSearch = this.fullSearchArea();
    const info = this.information();
    const pagination = this.pagination();

    let schema = this.options.schema
    const table = `<div class="kalipso-table-wrapper` + (this.customize.tableWrapClass ? ` ` + this.customize.tableWrapClass : ``) + `">` +
      `<table class="kalipso-table` + (this.customize.tableClass ? ` ` + this.customize.tableClass : ``) + `">` +
      this.head() +
      this.body() +
      this.footer() +
      `</table>` +
      `</div>`;

    schema = schema.replace("[T]", table);
    schema = schema.replace("[L]", sorting);
    schema = schema.replace("[S]", fullSearch);
    schema = schema.replace("[I]", info);
    schema = schema.replace("[P]", pagination);


    this.parent.innerHTML = schema;

    await this.prepareBody(true);
  }


  /**
   * Prepare sorting DOM. 
   * @return string
   */
  sortingSelect() {

    let sortingDom = ``;
    if (this.options.lengthOptions) {
      let defaultSelected = false;
      let selected = ``;

      sortingDom = `<select data-perpage` +
        (this.customize.selectClass !== undefined && this.customize.selectClass
          ? ` class="` + this.customize.selectClass + `" ` : ` `) +
        `>`;

      for (let i = 0; i < this.options.lengthOptions.length; i++) {
        if (this.options.lengthOptions[i].default !== undefined && this.options.lengthOptions[i].default) {
          defaultSelected = this.options.lengthOptions[i].value;
        }
      }

      for (let i = 0; i < this.options.lengthOptions.length; i++) {

        const val = this.options.lengthOptions[i].value.toString();
        const name = this.options.lengthOptions[i].name;
        selected = ``;

        if (defaultSelected === this.options.lengthOptions[i].value || (defaultSelected === false && i === 0)) {
          selected = ` selected`;
          if (defaultSelected === false && i === 0) {
            defaultSelected = this.options.lengthOptions[i].value;
          }
        }

        sortingDom = sortingDom.concat(`<option value="` + val + `"` + selected + `>` + (name === '[ALL]' ? this.l10n('all') : name) + `</option>`);
      }

      sortingDom = sortingDom.concat(`</select>`);
      this.options.length = defaultSelected;
    }

    return sortingDom;
  }


  /**
   * Prepare sorting DOM. 
   * @return string
   */
  fullSearchArea() {

    let area = ``;
    if (this.options.fullSearch) {
      area = `<input data-full-search type="text" placeholder="` + this.l10n("search") + `" class="` + this.options.customize.inputClass + `"/>`;
    }
    return area;
  }


  /**
   * Table information text.
   * @param boolean withParent  parent element option
   * @return string
   */
  information(withParent = true) {

    let info = ``;
    if (this.result && this.current !== 0) {
      info = this.l10n("showing_x_records_from_y_records").replace("[X]", this.current)
      let range = (this.current - this.options.length);
      range = range <= 0 ? 1 : range;
      info = info.replace("[Y]", range + ' - ' + this.current);
    } else {
      info = this.l10n("no_record");
    }

    if (this.current > 0 && this.current !== this.total) {
      info = info + ` (` + this.l10n("out_of_x_records").replace("[X]", this.total) + `)`;
    }
    return withParent ? `<span class="kalipso-information" data-info>` + info + `</span>` : info;
  }


  /**
   * Prepare pagination DOM.    
   * @param boolean withParent  parent element option
   * @return string
   */
  pagination(withParent = true) {

    let pagination = ``
    let page = this.page;

    let pageCount = this.options.length <= 0 ? 1 : Math.ceil(this.total / this.options.length);
    if (pageCount <= 0) pageCount = 1;

    if (pageCount < page && page > 0) {
      page = pageCount;
      this.page = page;
    }

    pagination = `<ul` + (this.customize.paginationUlClass ? ` class="` + this.customize.paginationUlClass + `"` : ``) + `>`

    if (this.result && this.current !== 0) {

      let firstAttr = ` disabled`
      if (page > 1) {
        firstAttr = ` data-page="` + 1 + `"`
      }

      pagination = pagination + `<li` + (this.customize.paginationUlClass ? ` class="` + this.customize.paginationUlClass + `"` : ``) + `>` +
        `<a` + (this.customize.paginationAClass ? ` class="` + this.customize.paginationAClass + `"` : ``) + ` href="javascript:;"` + firstAttr + `>` + this.l10n("first") + `</a>` +
        `</li>`

      let prevAttr = ` disabled`
      if (page > 1) {
        prevAttr = ` data-page="` + (page - 1) + `"`
      }

      pagination = pagination + `<li` + (this.customize.paginationUlClass ? ` class="` + this.customize.paginationUlClass + `"` : ``) + `>` +
        `<a` + (this.customize.paginationAClass ? ` class="` + this.customize.paginationAClass + `"` : ``) + ` href="javascript:;"` + prevAttr + `>` + this.l10n("prev") + `</a>` +
        `</li>`

      let firstFake = false;
      let lastFake = false;
      for (let i = 1; i <= pageCount; i++) {

        let continueNext = false;
        if (i < (page - 3)) {
          if (! firstFake) {
            firstFake = true;
            pagination = pagination + `<li` + (this.customize.paginationUlClass ? ` class="` + this.customize.paginationUlClass + `"` : ``) + `>` +
            `<a href="javascript:;" disabled>...</a>` +
            `</li>`
          }
          continueNext = true
        }
        if (i > (page + 3)) {
          if (! lastFake) {
            lastFake = true;
            pagination = pagination + `<li` + (this.customize.paginationUlClass ? ` class="` + this.customize.paginationUlClass + `"` : ``) + `>` +
            `<a href="javascript:;" disabled>...</a>` +
            `</li>`
          }
          continueNext = true
        }

        if (! continueNext) {

          let aClass = page === i ? `active` : ``
          aClass = aClass + (this.customize.paginationAClass ? (aClass === `` ? `` : ` `) + this.customize.paginationAClass : ``)
          pagination = pagination + `<li` + (this.customize.paginationUlClass ? ` class="` + this.customize.paginationUlClass + `"` : ``) + `>` +
            `<a` + (aClass !== `` ? ` class="` + aClass + `"` : ``) + ` href="javascript:;" data-page="` + i + `">` + i + `</a>` +
            `</li>`
        }
      }

      let nextAttr = ` disabled`
      if (page < pageCount) {
        nextAttr = ` data-page="` + (page + 1) + `"`
      }

      pagination = pagination + `<li` + (this.customize.paginationUlClass ? ` class="` + this.customize.paginationUlClass + `"` : ``) + `>` +
        `<a` + (this.customize.paginationAClass ? ` class="` + this.customize.paginationAClass + `"` : ``) + ` href="javascript:;"` + nextAttr + `>` + this.l10n("next") + `</a>` +
        `</li>`

      let lastAttr = ` disabled`
      if (page < pageCount) {
        lastAttr = ` data-page="` + pageCount + `"`
      }

      pagination = pagination + `<li` + (this.customize.paginationUlClass ? ` class="` + this.customize.paginationUlClass + `"` : ``) + `>` +
        `<a` + (this.customize.paginationAClass ? ` class="` + this.customize.paginationAClass + `"` : ``) + ` href="javascript:;"` + lastAttr + `>` + this.l10n("last") + `</a>` +
        `</li>`
    }

    pagination = pagination + `</ul>`

    return withParent ? `<nav class="kalipso-pagination" data-pagination>` + pagination + `</nav>` : pagination
  }

  /**
   * Prepares the table header.
   * @return string
   */
  head() {

    let thead = `<thead` + (this.options.customize.tableHeadClass ? ` class="` + this.options.customize.tableHeadClass + `"` : ``) + `><tr>`

    for (const [index, col] of Object.entries(this.options.columns)) {

      let thClass = 'sort'
      let sortingTitle = this.l10n("sorting_asc")
      if (this.options.order[0] !== undefined && this.options.order[0] === col.key) {
        thClass += ` ` + this.options.order[1]
        sortingTitle = this.l10n("sorting_" + (this.options.order[1] === "desc" ? "asc" : "desc"))
      }

      thead += `<th` + (col.orderable ? ` class="` + thClass + `" data-sort="` + col.key + `" title="` + sortingTitle + `"` : ``) + `>` + col.title + `</th>`

    }

    if (this.options.tableHeader.searchBar) {

      thead += `</tr><tr>`

      for (const [index, col] of Object.entries(this.options.columns)) {

        thead += this.options.tableFooter.searchBar ? `<th>` +
          (!col.searchable ? `` : this.generateSearchArea(col.searchable, col.key)) +
          `</th>` : `<th></th>`

      }

    }

    thead += `</tr></thead>`
    return thead
  }

  /**
   * Prepares the table body.
   * @param boolean withBodyTag   
   * @return string
   */
  body(withBodyTag = true) {

    let tbody = ``

    if (this.loading) {

      for (let i = 1; i <= this.options.length; i++) {
        tbody += `<tr class="kn-loading-body">`;
        for (const [index, col] of Object.entries(this.options.columns)) {

          tbody += `<td><div class="kt-skeleton-bar"></div></td>`;

        }
        tbody += `</tr>`;
      }

    } else if (this.result.length === 0) {

      tbody = `<tr><td colspan="100%" class="no_result_info">` + this.l10n("no_record") + `</td></tr>`

    } else {

      this.result.forEach((row) => {
        tbody += `<tr>`
        for (const [index, col] of Object.entries(this.options.columns)) {

          if (row[col.key] !== undefined) tbody += `<td>` + row[col.key] + `</td>`
          else tbody += `<td></td>`

        }
        tbody += `</tr>`
      })

    }

    return withBodyTag ? 
      `<tbody` + (this.options.customize.tableBodyClass ? ` class="` + this.options.customize.tableBodyClass + `"` : ``) + `>` + 
        tbody + 
      `</tbody>` 
      : tbody
  }

  /**
   * Prepares the table footer.
   * @return string
   */
  footer() {

    let tfoot = ``
    if (this.options.tableFooter.visible) {

      tfoot = `<tfoot` + (this.options.customize.tableFooterClass ? ` class="` + this.options.customize.tableFooterClass + `"` : ``) + `><tr>`

      for (const [index, col] of Object.entries(this.options.columns)) {

        tfoot += this.options.tableFooter.searchBar ? `<td>` +
          (!col.searchable ? col.title : this.generateSearchArea(col.searchable, col.key)) +
          `</td>` : `<td>` + col.title + `</td>`

      }

      tfoot += `</tr></tfoot>`
    }
    return tfoot
  }


  /**
   * Prepare content with options
   * @return Promise
   */
  prepareBody(firstLoad = false) {
    return new Promise(async (resolve) => {

      if (!this.server) { // client-side
        this.loading = true;
        document.querySelector(this.selector + ' tbody').innerHTML = this.body(false);
        
        let results = [...this.options.source];
        if (Object.keys(this.searchParams).length) { // search
          for (const [key, value] of Object.entries(this.searchParams)) {
            let _result = [];
            results.forEach((p) => {
              if (p[key] !== undefined) {
                let string = p[key];
                string = string.toString();
                if (string.indexOf(value) >= 0) {
                  _result.push(p)
                }
              }
            });
            results = [...new Set(_result)];
            this.page = 1;
          }
        }

        if (results.length && this.options.fullSearch && this.search) { // full search
          let tempResults = []
          results.forEach((p) => {
            for (const [key, value] of Object.entries(p)) {
              let string = value.toString()
              if (string.indexOf(this.search) >= 0) {
                tempResults.push(p)
                break;
              }
            }
          })
          results = tempResults
          this.page = 1;
        }

        if (results.length && this.options.order.length) { // order
          const orderKey = this.options.order[0];
          const orderType = this.options.order[1];
          results = results.sort((a, b) => {
            let x = a[orderKey];
            let y = b[orderKey];
            return orderType === 'asc' 
              ? ((x < y) ? -1 : ((x > y) ? 1 : 0))
              : ((x > y) ? -1 : ((x < y) ? 1 : 0));
          });
        }

        let start = this.page <= 1 ? 0 : ((this.page-1) * this.options.length)
        results = results.slice(start, (start + this.options.length));

        this.loading = false;
        this.total = this.options.source.length
        this.result = results;
        this.totalPage = this.result.length <= 0 ? 1 : Math.ceil(this.total / this.options.length);
        this.current = this.result.length;

      } else { // server-side
        this.loading = true;
        document.querySelector(this.selector + ' tbody').innerHTML = this.body(false);

        const controller = new AbortController();

        let fetchOptions = {
          method: 'GET',
          mode: 'cors',
          cache: 'no-cache',
          credentials: 'same-origin',
          headers: {
            'X-KALIPSOTABLE': this.version
          },
          redirect: 'follow',
          referrerPolicy: 'same-origin',
          signal: controller.signal,
        }

        const form = {};
        form.per_page = this.options.length;
        form.page = this.page;
        form.order = this.options.order[0] + ',' + this.options.order[1];
        if (this.search) {
          form.full_search = this.search;
        }
        form.search = encodeURI(JSON.stringify(this.searchParams));

        const link = this.options.source + '?' + (new URLSearchParams(form));
        const result = await fetch(link, fetchOptions).then(function (response) {

          return response.ok ? response.json() : false

        }).then(function (response) {

          return response

        }).catch(function (err) {
          return false
        })

        this.loading = false;
        if (result) {
          this.result = result.records;
          this.page = result.current_page;
          this.total = result.record_count;
          this.totalPage = result.total_page;
          this.current = result.filtered_count;
        } else {
          this.result = [];
          this.page = 1;
          this.total = 0;
          this.totalPage = 0;
          this.current = 0;
          this.bomb(this.l10n('server_response_problem'), 'error');
        }
      }

      document.querySelector(this.selector + ' tbody').innerHTML = this.body(false);
      document.querySelector(this.selector + ' [data-info]').innerHTML = this.information(false);
      document.querySelector(this.selector + ' [data-pagination]').innerHTML = this.pagination(false);

      setTimeout(() => {
        this.eventListener(firstLoad);
        resolve();
      }, 10)
    })
  }


  /**
   * Full search action.
   * @param object el    search input
   */
  async fullSearch(el) {
    this.search = el.value
    this.page = 1;
    await this.prepareBody()
  }


  /**
   * Record lenght action.
   * @param object el    per page select
   */
  async perPage(el) {
    this.options.length = parseInt(el.value)
    if (this.options.length === 0) this.options.length = this.total;
    this.page = 1
    await this.prepareBody()
  }

  
  /**
   * Switch page.
   * @param object el    page button
   */
  async switchPage(el) {
    
    let param = parseInt(el.getAttribute("data-page"))

    let pageCount = this.options.length <= 0 ? 1 : Math.ceil(this.total / this.options.length)
    if (param > pageCount) {
      this.page = pageCount
    } else if (param < 1) {
      this.page = 1
    } else {
      this.page = param
    }
    await this.prepareBody()
  }

  /**
   * Ordering the records.
   * @param object el       clicked button
   * @param integer index   for change action
   */
  async sort(element, index) {

    if (Array.from(element.classList).indexOf("asc") !== -1) { // asc

      element.classList.remove("asc")
      element.classList.add("desc")
      element.setAttribute("title", this.l10n("sorting_asc"))
      this.options.order = [element.getAttribute("data-sort"), "desc"]

    } else if (Array.from(element.classList).indexOf("desc") !== -1) { // desc

      element.classList.remove("desc")
      element.classList.add("asc")
      element.setAttribute("title", this.l10n("sorting_desc"))
      this.options.order = [element.getAttribute("data-sort"), "asc"]

    } else { // default

      element.classList.add("asc")
      element.setAttribute("title", this.l10n("sorting_desc"))
      this.options.order = [element.getAttribute("data-sort"), "asc"]

    }

    let thAreas = document.querySelectorAll(this.options.selector + ' thead th.sort')
    if (thAreas.length) {
      for (let thIndex = 0; thIndex < thAreas.length; thIndex++) {
        if (thIndex !== index) thAreas[thIndex].classList.remove("asc", "desc")
      }
    }
    await this.prepareBody();
  }

  /**
   * Clean tags.
   * @param string text  content
   * @return string
   */
  strip(text) {
    let tmp = document.createElement("DIV");
    tmp.innerHTML = text;
    return tmp.textContent || tmp.innerText || "";
  }


  /**
   * Prepares search fields for in-table searches.
   * @param object areaDatas  search area attributes
   * @param string key        area keyword
   * @return string
   */
  generateSearchArea(areaDatas, key) {

    // number | text | date | select
    let bar = ``
    switch (areaDatas.type) {
      case "number":
      case "text":
      case "date":
        bar = `<input data-search="` + key + `" type="` + areaDatas.type + `"` +
          (this.options.customize.inputClass !== undefined && this.options.customize.inputClass ? ` class="` + this.options.customize.inputClass + `" ` : ` `) +
          (areaDatas.min !== undefined && areaDatas.min ? ` min="` + areaDatas.min + `" ` : ` `) +
          (areaDatas.max !== undefined && areaDatas.max ? ` max="` + areaDatas.max + `" ` : ` `) +
          (areaDatas.maxlength !== undefined && areaDatas.maxlength ? ` maxlength="` + areaDatas.maxlength + `" ` : ` `) +
          `/>`
        break;

      case "select":
        bar = `<select data-search="` + key + `"` +
          (this.options.customize.selectClass !== undefined && this.options.customize.selectClass ? ` class="` + this.options.customize.selectClass + `" ` : ` `) +
          `><option value=""></option>`

        for (const [index, option] of Object.entries(areaDatas.datas)) {
          bar += `<option value="` + option.value + `">` + option.name + `</option>`
        }

        bar += `</select>`
        break;
    }
    return bar
  }


  /**
   * Prepares event listeners so that table actions can be listened to.
   * 
   */
  eventListener(firstLoad = false) {

    let that = this;
    if (firstLoad) {

      this.parent.addEventListener("click", function (event) {
        let target = event.target;
        if (target.nodeName === "TH" && target.hasAttribute("data-sort")) {
          event.preventDefault();
          that.sort(target, target.cellIndex);
        }
      }, {capture: true});

      this.parent.addEventListener("change", function (e) {
        let target = e.target;
        if (target.nodeName === "SELECT" && target.hasAttribute("data-perpage")) {
          e.preventDefault();
          that.perPage(target);
        } else if (target.nodeName === "SELECT" && target.hasAttribute("data-search")) {
          e.preventDefault();
          that.fieldSynchronizer(target);
        }

      }, {capture: true});

      this.parent.addEventListener("keyup", function (e) {
          let target = event.target;
          if (e.target.nodeName === "INPUT" && target.hasAttribute("data-search")) {
            e.preventDefault();
            that.fieldSynchronizer(target);
          } else if (e.target.nodeName === "INPUT" && target.hasAttribute("data-full-search")) {
            e.preventDefault();
            that.fullSearch(target);
          }
      }, {capture: true});

    } else {

      let el = document.querySelector(this.selector + ' [data-pagination]'),
        elClone = el.cloneNode(true);

      el.parentNode.replaceChild(elClone, el);

    }

    document.querySelector(this.selector + ' [data-pagination]').addEventListener("click", function (event) {
      let target = event.target;
      if (target.nodeName === "A" && target.hasAttribute("data-page")) {
        event.preventDefault();
        that.switchPage(target);
      }
    }, {capture: true});
    
  }


  /**
   * If there is more than one of the changing search fields, it ensures that all search fields are synchronized with the same data.
   * @param object field
   */
  async fieldSynchronizer(field) {
    let searchAttr = field.getAttribute("data-search");
    const targetElements = document.querySelectorAll(this.options.selector + ` [data-search="` + searchAttr + `"]`)
    targetElements.forEach((input) => {
      input.value = field.value
    })
    let val = field.value;
    if (field.getAttribute('type') === 'date') {
      val = val.replaceAll('-', '.');
    }
    this.searchParams[searchAttr] = val

    // clear empty string parameters
    let tempParams = {}
    for (const [key, value] of Object.entries(this.searchParams)) {
      if (value !== "") tempParams[key] = value
    }
    this.searchParams = tempParams
    await this.prepareBody()
  }
}