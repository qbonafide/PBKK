using System;
using System.Linq;
using System.Text.RegularExpressions;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Input;

namespace StudentRegistrationApp
{
    public partial class MainWindow : Window
    {
        private int _selectedId = 0;

        public MainWindow()
        {
            InitializeComponent();
            LoadData();
        }

        private void LoadData()
        {
            try
            {
                var data = Database.GetAll(txtSearch.Text.Trim());
                lstMahasiswa.ItemsSource = data;
                lblJumlah.Text = $"Jumlah Mahasiswa: {data.Count}";
            }
            catch (Exception ex)
            {
                MessageBox.Show("Gagal terhubung ke database:\n" + ex.Message,
                                "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        private void TxtSearch_TextChanged(object sender, TextChangedEventArgs e)
        {
            if (!IsLoaded) return;
            LoadData();
        }

        // ============================================================
        //  CREATE
        // ============================================================
        private void BtnSimpan_Click(object sender, RoutedEventArgs e)
        {
            if (!ValidateForm(0)) return;

            try
            {
                Database.Insert(BuildMahasiswa());
                MessageBox.Show("Data mahasiswa berhasil disimpan!", "Informasi",
                                MessageBoxButton.OK, MessageBoxImage.Information);
                ClearForm();
                LoadData();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Gagal menyimpan data:\n" + ex.Message,
                                "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        // ============================================================
        //  UPDATE
        // ============================================================
        private void BtnUpdate_Click(object sender, RoutedEventArgs e)
        {
            if (_selectedId == 0)
            {
                MessageBox.Show("Pilih data yang ingin diedit dari daftar!");
                return;
            }

            if (!ValidateForm(_selectedId)) return;

            try
            {
                var m = BuildMahasiswa();
                m.Id = _selectedId;
                Database.Update(m);

                MessageBox.Show("Data mahasiswa berhasil diupdate!", "Informasi",
                                MessageBoxButton.OK, MessageBoxImage.Information);
                ClearForm();
                LoadData();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Gagal mengupdate data:\n" + ex.Message,
                                "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        // ============================================================
        //  RESET
        // ============================================================
        private void BtnReset_Click(object sender, RoutedEventArgs e)
        {
            ClearForm();
        }

        // ============================================================
        //  DELETE + KONFIRMASI
        // ============================================================
        private void BtnHapus_Click(object sender, RoutedEventArgs e)
        {
            if (lstMahasiswa.SelectedItem is not Mahasiswa m)
            {
                MessageBox.Show("Pilih data yang ingin dihapus!");
                return;
            }

            var konfirmasi = MessageBox.Show(
                $"Yakin ingin menghapus data berikut?\n\n{m.Nim} - {m.Nama}",
                "Konfirmasi Hapus",
                MessageBoxButton.YesNo,
                MessageBoxImage.Question);

            if (konfirmasi != MessageBoxResult.Yes) return;

            try
            {
                Database.Delete(m.Id);
                ClearForm();
                LoadData();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Gagal menghapus data:\n" + ex.Message,
                                "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        // ============================================================
        //  PILIH DATA DI LISTBOX -> ISI FORM (untuk edit)
        // ============================================================
        private void LstMahasiswa_SelectionChanged(object sender, SelectionChangedEventArgs e)
        {
            if (lstMahasiswa.SelectedItem is not Mahasiswa m)
            {
                _selectedId = 0;
                btnUpdate.IsEnabled = false;
                return;
            }

            _selectedId = m.Id;
            btnUpdate.IsEnabled = true;

            txtNim.Text = m.Nim;
            txtNama.Text = m.Nama;
            txtAlamat.Text = m.Alamat;
            txtTelepon.Text = m.NoTelepon;
            dtpLahir.SelectedDate = m.TanggalLahir;

            cmbProdi.SelectedIndex = -1;
            foreach (ComboBoxItem item in cmbProdi.Items)
            {
                if (item.Content.ToString() == m.Prodi)
                {
                    cmbProdi.SelectedItem = item;
                    break;
                }
            }

            rbLaki.IsChecked = m.JenisKelamin == "Laki-laki";
            rbPerempuan.IsChecked = m.JenisKelamin == "Perempuan";
        }

        // ============================================================
        //  BLOKIR INPUT NON-ANGKA (NIM & Telepon)
        // ============================================================
        private void Angka_PreviewTextInput(object sender, TextCompositionEventArgs e)
        {
            e.Handled = !e.Text.All(char.IsDigit);
        }

        // ============================================================
        //  VALIDASI
        // ============================================================
        private bool ValidateForm(int excludeId)
        {
            string nim = txtNim.Text.Trim();
            string nama = txtNama.Text.Trim();
            string alamat = txtAlamat.Text.Trim();
            string telp = txtTelepon.Text.Trim();

            // NIM
            if (string.IsNullOrWhiteSpace(nim))
            {
                MessageBox.Show("NIM harus diisi!");
                txtNim.Focus();
                return false;
            }
            if (!Regex.IsMatch(nim, @"^\d{8,12}$"))
            {
                MessageBox.Show("NIM harus berupa angka, 8 sampai 12 digit!");
                txtNim.Focus();
                return false;
            }

            // Nama
            if (string.IsNullOrWhiteSpace(nama))
            {
                MessageBox.Show("Nama harus diisi!");
                txtNama.Focus();
                return false;
            }
            if (nama.Length < 3 || !Regex.IsMatch(nama, @"^[\p{L} .'\-]+$"))
            {
                MessageBox.Show("Nama minimal 3 karakter dan hanya boleh berisi huruf!");
                txtNama.Focus();
                return false;
            }

            // Prodi
            if (cmbProdi.SelectedItem == null)
            {
                MessageBox.Show("Pilih program studi!");
                cmbProdi.Focus();
                return false;
            }

            // Jenis kelamin
            if (rbLaki.IsChecked != true && rbPerempuan.IsChecked != true)
            {
                MessageBox.Show("Pilih jenis kelamin!");
                return false;
            }

            // Tanggal lahir
            if (dtpLahir.SelectedDate == null)
            {
                MessageBox.Show("Tanggal lahir harus diisi!");
                dtpLahir.Focus();
                return false;
            }
            if (dtpLahir.SelectedDate.Value.Date > DateTime.Today.AddYears(-15))
            {
                MessageBox.Show("Tanggal lahir tidak valid (usia minimal 15 tahun)!");
                dtpLahir.Focus();
                return false;
            }

            // Alamat
            if (alamat.Length < 5)
            {
                MessageBox.Show("Alamat harus diisi (minimal 5 karakter)!");
                txtAlamat.Focus();
                return false;
            }

            // Telepon
            if (string.IsNullOrWhiteSpace(telp))
            {
                MessageBox.Show("Nomor telepon harus diisi!");
                txtTelepon.Focus();
                return false;
            }
            if (!Regex.IsMatch(telp, @"^(08|628)\d{8,11}$"))
            {
                MessageBox.Show("Nomor telepon harus diawali 08 atau 628, total 10-13 digit!");
                txtTelepon.Focus();
                return false;
            }

            // NIM tidak boleh duplikat
            try
            {
                if (Database.NimExists(nim, excludeId))
                {
                    MessageBox.Show("NIM sudah terdaftar!");
                    txtNim.Focus();
                    return false;
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show("Gagal mengecek NIM:\n" + ex.Message,
                                "Error", MessageBoxButton.OK, MessageBoxImage.Error);
                return false;
            }

            return true;
        }

        private Mahasiswa BuildMahasiswa()
        {
            string prodi = "";
            if (cmbProdi.SelectedItem is ComboBoxItem item)
            {
                prodi = item.Content.ToString() ?? "";
            }

            string jenisKelamin = rbLaki.IsChecked == true ? "Laki-laki" : "Perempuan";

            return new Mahasiswa
            {
                Nim = txtNim.Text.Trim(),
                Nama = txtNama.Text.Trim(),
                Prodi = prodi,
                JenisKelamin = jenisKelamin,
                TanggalLahir = dtpLahir.SelectedDate!.Value.Date,
                Alamat = txtAlamat.Text.Trim(),
                NoTelepon = txtTelepon.Text.Trim()
            };
        }

        private void ClearForm()
        {
            txtNim.Clear();
            txtNama.Clear();
            txtAlamat.Clear();
            txtTelepon.Clear();
            cmbProdi.SelectedIndex = -1;
            rbLaki.IsChecked = false;
            rbPerempuan.IsChecked = false;
            dtpLahir.SelectedDate = null;
            lstMahasiswa.SelectedIndex = -1;
            _selectedId = 0;
            btnUpdate.IsEnabled = false;
            txtNim.Focus();
        }
    }
}
