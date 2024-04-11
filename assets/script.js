class App {
  constructor() {
    this.requestTable = null;
    this.init();
  }

  init() {
    this.prepareHolidayRequestTable();
    this.handleGetHolidayRequest();
    this.handleHolidayAction();
  }

  prepareHolidayRequestTable() {
    this.requestTable = new DataTable("#holiday-table", {
      ajax: {
        url: "/app.php?controller=user&action=getAll",
        dataSrc: "",
      },
      columns: [
        { data: "first_name" },
        { data: "last_name" },
        { data: "department" },
        { data: "to" },
      ],
      columnDefs: [
        {
          targets: 3,
          render: function (data, type, row) {
            return ` <button class="icon btn btn-primary btn-sm view-holiday-btn" data-id="${row.id}"> <i class="fa fa-eye"></i> </button>`;
          },
        },
      ],
    });
  }

  handleGetHolidayRequest() {
    $(document).on("click", ".view-holiday-btn", function () {
      const id = $(this).data("id");
      if (!id) {
        alert("Something went wrong. Please try again later.");
        return;
      }
      $.ajax({
        url: "/app.php?controller=holiday&action=getAll",
        data: { userID: id },
        dataType: "json",
        success: (response) => {
          if (response.length === 0) {
            alert("No Holiday request found");
            return;
          }
          const tableHtml = response
            .map(
              (item) => `
            <tr>
              <td>${item.reason}</td>
              <td>${item.from}</td>
              <td>${item.to}</td>
              <td>
                <button type="button" class="btn btn-success holiday-action" data-id="${item.id}" data-status="A">Approve</button>
                <button type="button" class="btn btn-danger holiday-action" data-id="${item.id}" data-status="D">Deny</button>
              </td>
            </tr>
          `
            )
            .join("");
          $("#holiday-table-container").html(tableHtml);
          new bootstrap.Modal($("#holidayModal")).show();
        },
      });
    });
  }

  handleHolidayAction() {
    let _this = this;

    $(document).on("click", ".holiday-action", function () {
      const id = $(this).data("id");
      const status = $(this).data("status");
      if (!id || !status) {
        alert("Something went wrong. Please try again later.");
        return;
      }
      $.ajax({
        url: "/app.php?controller=holiday&action=updateStatus",
        method: "POST",
        data: { id, status },
        dataType: "json",
        success: (response) => {
          console.log(response);
          if (response.success) {
            alert("Holiday action updated successfully.");
            // Refresh the holiday request table
            _this.requestTable.ajax.reload();
            bootstrap.Modal.getInstance($("#holidayModal")).hide();
          } else {
            alert("Failed to update holiday action.");
          }
        },
        error: () => {
          alert("An error occurred while updating holiday action.");
        },
      });
    });
  }
}

// Initialize the App class
const app = new App();
