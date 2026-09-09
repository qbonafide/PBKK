using System.Windows.Forms;

namespace SistemDataMahasiswa;

public class FormUtama : Form
{
    private TextBox txtNim = default!;
    private TextBox txtNama = default!;
    private TextBox txtProdi = default!;
    private TextBox txtIpk = default!;

    private TextBox txtCari = default!;

    private DataGridView grid = default!;

    public FormUtama()
    {
        InitializeUi();
        DatabaseHelper.Inisialisasi();
        MuatData();
    }

    private void InitializeUi()
    {
        Text = "Sistem Data Mahasiswa";
        StartPosition = FormStartPosition.CenterScreen;
        ClientSize = new Size(960, 560);
        Font = new Font("Segoe UI", 10F);

        var lblJudul = new Label
        {
            Text = "SISTEM DATA MAHASISWA",
            Font = new Font("Segoe UI", 16F, FontStyle.Bold),
            Dock = DockStyle.Top,
            TextAlign = ContentAlignment.MiddleCenter,
            Height = 50
        };

        var panelInput = new GroupBox
        {
            Text = "Tambah Mahasiswa",
            Dock = DockStyle.Top,
            Height = 110,
            Padding = new Padding(14)
        };

        txtNim = BuatField(panelInput, "NIM:", 15, 28, 120);
        txtNama = BuatField(panelInput, "Nama:", 165, 28, 240);
        txtProdi = BuatField(panelInput, "Prodi:", 435, 28, 160);
        txtIpk = BuatField(panelInput, "IPK:", 625, 28, 90);

        var btnTambah = new Button
        {
            Text = "Tambah",
            AutoSize = true,
            AutoSizeMode = AutoSizeMode.GrowAndShrink,
            Location = new Point(755, 44),
            BackColor = Color.FromArgb(46, 125, 50),
            ForeColor = Color.White,
            FlatStyle = FlatStyle.Flat,
            Padding = new Padding(12, 8, 12, 8)
        };
        btnTambah.Click += (_, _) => TambahMahasiswa();
        panelInput.Controls.Add(btnTambah);

        var panelCari = new GroupBox
        {
            Text = "Cari / Hapus",
            Dock = DockStyle.Top,
            Height = 105,
            Padding = new Padding(14)
        };

        panelCari.Controls.Add(new Label
        {
            Text = "NIM:",
            Location = new Point(15, 46),
            AutoSize = true
        });
        txtCari = new TextBox
        {
            Location = new Point(75, 42),
            Width = 260,
            Height = 28
        };
        panelCari.Controls.Add(txtCari);

        var btnCari = new Button
        {
            Text = "Cari",
            AutoSize = true,
            AutoSizeMode = AutoSizeMode.GrowAndShrink,
            Location = new Point(370, 40),
            FlatStyle = FlatStyle.Flat,
            Padding = new Padding(12, 8, 12, 8)
        };
        btnCari.Click += (_, _) => CariMahasiswa();
        panelCari.Controls.Add(btnCari);

        var btnHapus = new Button
        {
            Text = "Hapus",
            AutoSize = true,
            AutoSizeMode = AutoSizeMode.GrowAndShrink,
            Location = new Point(520, 40),
            BackColor = Color.FromArgb(198, 40, 40),
            ForeColor = Color.White,
            FlatStyle = FlatStyle.Flat,
            Padding = new Padding(12, 8, 12, 8)
        };
        btnHapus.Click += (_, _) => HapusMahasiswa();
        panelCari.Controls.Add(btnHapus);

        grid = new DataGridView
        {
            Dock = DockStyle.Fill,
            AllowUserToAddRows = false,
            AllowUserToDeleteRows = false,
            ReadOnly = true,
            AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill,
            BackgroundColor = Color.White,
            RowHeadersVisible = false,
            SelectionMode = DataGridViewSelectionMode.FullRowSelect,
            GridColor = Color.FromArgb(224, 224, 224),
            BorderStyle = BorderStyle.None,
            RowTemplate = { Height = 32 },
            ColumnHeadersHeightSizeMode = DataGridViewColumnHeadersHeightSizeMode.DisableResizing,
            ColumnHeadersHeight = 40
        };

        grid.EnableHeadersVisualStyles = false;
        grid.ColumnHeadersDefaultCellStyle = new DataGridViewCellStyle
        {
            BackColor = Color.FromArgb(33, 150, 243),
            ForeColor = Color.White,
            Font = new Font("Segoe UI", 10F, FontStyle.Bold),
            Alignment = DataGridViewContentAlignment.MiddleCenter
        };
        grid.DefaultCellStyle = new DataGridViewCellStyle
        {
            BackColor = Color.White,
            ForeColor = Color.Black,
            SelectionBackColor = Color.FromArgb(187, 222, 251),
            SelectionForeColor = Color.Black,
            Padding = new Padding(4),
            Alignment = DataGridViewContentAlignment.MiddleLeft
        };
        grid.AlternatingRowsDefaultCellStyle = new DataGridViewCellStyle
        {
            BackColor = Color.FromArgb(245, 247, 250)
        };

        Controls.Add(grid);
        Controls.Add(panelCari);
        Controls.Add(panelInput);
        Controls.Add(lblJudul);
    }

    private TextBox BuatField(GroupBox parent, string label, int x, int y, int textBoxWidth)
    {
        var panel = new Panel
        {
            Size = new Size(textBoxWidth + 10, 60),
            Location = new Point(x, y)
        };

        panel.Controls.Add(new Label
        {
            Text = label,
            Location = new Point(0, 0),
            AutoSize = true
        });

        var textBox = new TextBox
        {
            Location = new Point(0, 24),
            Width = textBoxWidth,
            Height = 28
        };
        panel.Controls.Add(textBox);
        parent.Controls.Add(panel);
        return textBox;
    }

    private void MuatData()
    {
        grid.DataSource = null;
        grid.DataSource = DatabaseHelper.AmbilSemua();
    }

    private void TambahMahasiswa()
    {
        string nim = txtNim.Text.Trim();
        string nama = txtNama.Text.Trim();
        string prodi = txtProdi.Text.Trim();
        string ipkText = txtIpk.Text.Trim();

        if (nim.Length == 0 || nama.Length == 0 || prodi.Length == 0 || ipkText.Length == 0)
        {
            MessageBox.Show("Semua kolom harus diisi.", "Peringatan",
                MessageBoxButtons.OK, MessageBoxIcon.Warning);
            return;
        }

        if (!double.TryParse(ipkText, out double ipk) || ipk < 0 || ipk > 4)
        {
            MessageBox.Show("IPK harus berupa angka 0 - 4.", "Peringatan",
                MessageBoxButtons.OK, MessageBoxIcon.Warning);
            return;
        }

        try
        {
            DatabaseHelper.Tambah(new Mahasiswa
            {
                NIM = nim,
                Nama = nama,
                Prodi = prodi,
                IPK = ipk
            });

            MessageBox.Show("Data mahasiswa berhasil ditambahkan.", "Sukses",
                MessageBoxButtons.OK, MessageBoxIcon.Information);

            txtNim.Clear();
            txtNama.Clear();
            txtProdi.Clear();
            txtIpk.Clear();

            MuatData();
        }
        catch (Exception ex)
        {
            MessageBox.Show("Gagal menambahkan: " + ex.Message, "Error",
                MessageBoxButtons.OK, MessageBoxIcon.Error);
        }
    }

    private void CariMahasiswa()
    {
        string nim = txtCari.Text.Trim();
        if (nim.Length == 0)
        {
            MuatData();
            return;
        }

        var hasil = DatabaseHelper.Cari(nim);
        if (hasil is null)
        {
            MessageBox.Show("Mahasiswa dengan NIM tersebut tidak ditemukan.", "Hasil",
                MessageBoxButtons.OK, MessageBoxIcon.Information);
            MuatData();
            return;
        }

        grid.DataSource = null;
        grid.DataSource = new List<Mahasiswa> { hasil };
    }

    private void HapusMahasiswa()
    {
        string nim = txtCari.Text.Trim();
        if (nim.Length == 0)
        {
            MessageBox.Show("Masukkan NIM yang ingin dihapus terlebih dahulu.", "Peringatan",
                MessageBoxButtons.OK, MessageBoxIcon.Warning);
            return;
        }

        var konfirmasi = MessageBox.Show(
            $"Yakin ingin menghapus mahasiswa dengan NIM {nim}?",
            "Konfirmasi", MessageBoxButtons.YesNo, MessageBoxIcon.Question);

        if (konfirmasi != DialogResult.Yes)
        {
            return;
        }

        if (DatabaseHelper.Hapus(nim))
        {
            MessageBox.Show("Data mahasiswa berhasil dihapus.", "Sukses",
                MessageBoxButtons.OK, MessageBoxIcon.Information);
            txtCari.Clear();
            MuatData();
        }
        else
        {
            MessageBox.Show("Data mahasiswa tidak ditemukan.", "Hasil",
                MessageBoxButtons.OK, MessageBoxIcon.Information);
        }
    }
}
